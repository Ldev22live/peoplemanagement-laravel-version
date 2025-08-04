<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;
    
    private function __construct()
    {
        $dbParams = [
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'dbname'   => 'people_management',
            'user'     => 'peopleuser',
            'password' => 'securepassword',
        ];
        
        $dsn = "mysql:host={$dbParams['host']};port={$dbParams['port']};dbname={$dbParams['dbname']}";
        
        try {
            $this->pdo = new PDO($dsn, $dbParams['user'], $dbParams['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection()
    {
        return $this->pdo;
    }
    
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        
        // Bind each parameter with appropriate type
        foreach ($params as $key => $value) {
            $paramType = PDO::PARAM_STR;
            
            // If it's binary data, use PDO::PARAM_LOB
            if (is_string($value) && !mb_detect_encoding($value, 'UTF-8', true)) {
                $paramType = PDO::PARAM_LOB;
            } elseif (is_int($value)) {
                $paramType = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $paramType = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $paramType = PDO::PARAM_NULL;
            }
            
            // Handle both named and positional parameters
            $paramName = is_string($key) ? $key : $key + 1;
            $stmt->bindValue($paramName, $value, $paramType);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        // Return the last inserted ID
        return $this->pdo->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = [])
    {
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClauses);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $params = array_merge($data, $whereParams);
        $this->query($sql, $params);
    }
    
    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->query($sql, $params);
    }
}
