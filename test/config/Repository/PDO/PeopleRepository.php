<?php

namespace App\Repository\PDO;

require_once __DIR__ . '/../../../config/Database.php';

use App\Database;
use DateTime;
use PDO;

class PeopleRepository
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function find($id)
    {
        $sql = "SELECT p.*, l.name as language_name 
                FROM people p
                LEFT JOIN languages l ON p.language_id = l.id
                WHERE p.id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    public function findAll()
    {
        $sql = "SELECT p.*, l.name as language_name 
                FROM people p
                LEFT JOIN languages l ON p.language_id = l.id
                ORDER BY p.surname, p.name";
        return $this->db->fetchAll($sql);
    }
    
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $sql = "SELECT p.*, l.name as language_name 
                FROM people p
                LEFT JOIN languages l ON p.language_id = l.id
                WHERE ";
        $conditions = [];
        $params = [];
        
        foreach ($criteria as $column => $value) {
            $conditions[] = "p.{$column} = :{$column}";
            $params[$column] = $value;
        }
        
        $sql .= implode(' AND ', $conditions);
        
        if ($orderBy) {
            $orderClauses = [];
            foreach ($orderBy as $column => $direction) {
                $orderClauses[] = "p.{$column} {$direction}";
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        } else {
            $sql .= " ORDER BY p.surname, p.name";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function findOneBy(array $criteria)
    {
        $result = $this->findBy($criteria, null, 1);
        return $result ? $result[0] : null;
    }
    
    public function save(array $personData)
    {
        // Begin transaction
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();
        
        try {
            // Extract interests data if present
            $interests = isset($personData['interests']) ? $personData['interests'] : [];
            unset($personData['interests']);
            
            // If it's an update (person has an ID)
            if (isset($personData['id'])) {
                $id = $personData['id'];
                unset($personData['id']); // Remove ID from data to update
                
                // Update updated_at timestamp
                $personData['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
                
                $this->db->update('people', $personData, 'id = :id', ['id' => $id]);
                
                // Delete existing interests associations
                $pdo->prepare("DELETE FROM people_interests WHERE person_id = :person_id")
                    ->execute(['person_id' => $id]);
                
                // Insert new interests associations
                if (!empty($interests)) {
                    $stmt = $pdo->prepare("INSERT INTO people_interests (person_id, interest_id) VALUES (:person_id, :interest_id)");
                    foreach ($interests as $interestId) {
                        $stmt->execute([
                            'person_id' => $id,
                            'interest_id' => $interestId
                        ]);
                    }
                }
                
                $pdo->commit();
                return $id;
            } 
            // If it's a new person
            else {
                // Set created_at and updated_at timestamps
                $personData['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $personData['updated_at'] = $personData['created_at'];
                
                $id = $this->db->insert('people', $personData);
                
                // Insert interests associations
                if (!empty($interests) && $id) {
                    $stmt = $pdo->prepare("INSERT INTO people_interests (person_id, interest_id) VALUES (:person_id, :interest_id)");
                    foreach ($interests as $interestId) {
                        $stmt->execute([
                            'person_id' => $id,
                            'interest_id' => $interestId
                        ]);
                    }
                }
                
                $pdo->commit();
                return $id;
            }
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    public function delete($id)
    {
        // Begin transaction
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();
        
        try {
            // Delete interests associations first
            $pdo->prepare("DELETE FROM people_interests WHERE person_id = :person_id")
                ->execute(['person_id' => $id]);
            
            // Delete the person
            $this->db->delete('people', 'id = :id', ['id' => $id]);
            
            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    public function getPersonInterests($personId)
    {
        $sql = "SELECT i.* FROM interests i
                JOIN people_interests pi ON i.id = pi.interest_id
                WHERE pi.person_id = :person_id
                ORDER BY i.name";
        return $this->db->fetchAll($sql, ['person_id' => $personId]);
    }
    
    public function getAllLanguages()
    {
        $sql = "SELECT * FROM languages ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    
    public function getAllInterests()
    {
        $sql = "SELECT * FROM interests ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    
    public function validateIdNumber($idNumber)
    {
        // South African ID number validation
        // Format: YYMMDD SSSS 0 Z C
        // YYMMDD - Date of birth
        // SSSS - Gender (Females: 0000-4999, Males: 5000-9999)
        // 0 - Citizenship (0 for SA citizens, 1 for permanent residents)
        // Z - 8 (fixed value for modern IDs)
        // C - Checksum digit
        
        // Check length
        if (strlen($idNumber) !== 13) {
            return false;
        }
        
        // Check if all characters are digits
        if (!ctype_digit($idNumber)) {
            return false;
        }
        
        // Extract date components
        $year = substr($idNumber, 0, 2);
        $month = substr($idNumber, 2, 2);
        $day = substr($idNumber, 4, 2);
        
        // Add century prefix (19 or 20)
        $fullYear = $year > 20 ? "19{$year}" : "20{$year}";
        
        // Validate date
        if (!checkdate((int)$month, (int)$day, (int)$fullYear)) {
            return false;
        }
        
        // Check citizenship digit
        $citizenship = substr($idNumber, 10, 1);
        if ($citizenship !== '0' && $citizenship !== '1') {
            return false;
        }
        
        // Check control digit (Z) - should be 8 for modern IDs
        if (substr($idNumber, 11, 1) !== '8') {
            return false;
        }
        
        // Validate checksum (Luhn algorithm)
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$idNumber[$i];
            if ($i % 2 === 0) {
                $sum += $digit;
            } else {
                $sum += $digit * 2 > 9 ? $digit * 2 - 9 : $digit * 2;
            }
        }
        
        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === (int)$idNumber[12];
    }
}
