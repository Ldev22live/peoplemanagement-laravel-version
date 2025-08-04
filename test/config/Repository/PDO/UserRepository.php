<?php

namespace App\Repository\PDO;

require_once __DIR__ . '/../../../config/Database.php';

use App\Database;
use DateTime;

class UserRepository
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function find($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    public function findAll()
    {
        $sql = "SELECT * FROM users";
        return $this->db->fetchAll($sql);
    }
    
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM users WHERE ";
        $conditions = [];
        $params = [];
        
        foreach ($criteria as $column => $value) {
            $conditions[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }
        
        $sql .= implode(' AND ', $conditions);
        
        if ($orderBy) {
            $orderClauses = [];
            foreach ($orderBy as $column => $direction) {
                $orderClauses[] = "{$column} {$direction}";
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
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
    
    public function save($userData)
    {
        // If it's an update (user has an ID)
        if (isset($userData['id'])) {
            $id = $userData['id'];
            unset($userData['id']); // Remove ID from data to update
            
            // Update last_login if not already set
            if (!isset($userData['last_login'])) {
                $userData['last_login'] = (new DateTime())->format('Y-m-d H:i:s');
            }
            
            $this->db->update('users', $userData, 'id = :id', ['id' => $id]);
            return $id;
        } 
        // If it's a new user
        else {
            // Set registered timestamp if not already set
            if (!isset($userData['registered'])) {
                $userData['registered'] = (new DateTime())->format('Y-m-d H:i:s');
            }
            
            return $this->db->insert('users', $userData);
        }
    }
    
    public function updateProfilePicture($userId, $imageData)
    {
        $this->db->update(
            'users',
            ['profilepic' => $imageData, 'last_login' => (new DateTime())->format('Y-m-d H:i:s')],
            'id = :id',
            ['id' => $userId]
        );
    }
    
    public function delete($id)
    {
        $this->db->delete('users', 'id = :id', ['id' => $id]);
    }
}
