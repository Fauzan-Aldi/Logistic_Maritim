<?php
// Database configuration

require_once 'environment.php';

// Add this block to initialize $conn for MySQLi
$conn = new mysqli("localhost", "root", "", "logistik_maritim");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

class Database
{
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct()
    {
        $this->config = [
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS,
            'charset' => 'utf8mb4'
        ];

        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['name']};charset={$this->config['charset']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $this->connection = new PDO($dsn, $this->config['user'], $this->config['pass'], $options);
        } catch (PDOException $e) {
            log_error("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            log_error("Query failed: " . $e->getMessage(), ['sql' => $sql, 'params' => $params]);
            throw new Exception("Query failed");
        }
    }

    public function select($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function selectOne($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $fields = array_map(function ($field) {
            return "$field = ?";
        }, array_keys($data));

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $fields),
            $where
        );

        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }

    public function delete($table, $where, $params = [])
    {
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        return $this->query($sql, $params)->rowCount();
    }

    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollback()
    {
        return $this->connection->rollBack();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function quote($value)
    {
        return $this->connection->quote($value);
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

// Database helper functions
function db_query($sql, $params = [])
{
    return Database::getInstance()->query($sql, $params);
}

function db_select($sql, $params = [])
{
    return Database::getInstance()->select($sql, $params);
}

function db_select_one($sql, $params = [])
{
    return Database::getInstance()->selectOne($sql, $params);
}

function db_insert($table, $data)
{
    return Database::getInstance()->insert($table, $data);
}

function db_update($table, $data, $where, $whereParams = [])
{
    return Database::getInstance()->update($table, $data, $where, $whereParams);
}

function db_delete($table, $where, $params = [])
{
    return Database::getInstance()->delete($table, $where, $params);
}

function db_begin_transaction()
{
    return Database::getInstance()->beginTransaction();
}

function db_commit()
{
    return Database::getInstance()->commit();
}

function db_rollback()
{
    return Database::getInstance()->rollback();
}

function db_last_insert_id()
{
    return Database::getInstance()->lastInsertId();
}

function db_quote($value)
{
    return Database::getInstance()->quote($value);
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get database connection
function get_db_connection()
{
    global $conn;
    return $conn;
}

// Function to execute query with prepared statements
function execute_query($sql, $types = "", $params = [])
{
    global $conn;
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt;
}

// Function to get single row
function get_row($sql, $types = "", $params = [])
{
    $stmt = execute_query($sql, $types, $params);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get multiple rows
function get_rows($sql, $types = "", $params = [])
{
    $stmt = execute_query($sql, $types, $params);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to insert data
function insert_data($table, $data)
{
    global $conn;

    $columns = implode(", ", array_keys($data));
    $values = implode(", ", array_fill(0, count($data), "?"));
    $types = str_repeat("s", count($data));

    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
    $stmt = execute_query($sql, $types, array_values($data));

    return $conn->insert_id;
}

// Function to update data
function update_data($table, $data, $where, $where_types = "", $where_params = [])
{
    $set = implode(" = ?, ", array_keys($data)) . " = ?";
    $types = str_repeat("s", count($data)) . $where_types;
    $params = array_merge(array_values($data), $where_params);

    $sql = "UPDATE $table SET $set WHERE $where";
    $stmt = execute_query($sql, $types, $params);

    return $stmt->affected_rows;
}

// Function to delete data
function delete_data($table, $where, $types = "", $params = [])
{
    $sql = "DELETE FROM $table WHERE $where";
    $stmt = execute_query($sql, $types, $params);

    return $stmt->affected_rows;
}

// Function to begin transaction
function begin_transaction()
{
    global $conn;
    $conn->begin_transaction();
}

// Function to commit transaction
function commit_transaction()
{
    global $conn;
    $conn->commit();
}

// Function to rollback transaction
function rollback_transaction()
{
    global $conn;
    $conn->rollback();
}

// Function to escape string
function escape_string($string)
{
    global $conn;
    return $conn->real_escape_string($string);
}

// Function to get last error
function get_last_error()
{
    global $conn;
    return $conn->error;
}

// Function to get last insert ID
function get_last_insert_id()
{
    global $conn;
    return $conn->insert_id;
}

// Function to check if table exists
function table_exists($table)
{
    global $conn;
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result->num_rows > 0;
}

// Function to check if column exists
function column_exists($table, $column)
{
    global $conn;
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}
// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($sql);

// Create shipments table
$sql = "CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    origin VARCHAR(100),
    destination VARCHAR(100),
    shipment_date DATE,
    status VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($sql);

// Create shipment_details table
$sql = "CREATE TABLE IF NOT EXISTS shipment_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT NOT NULL,
    item_name VARCHAR(100),
    quantity INT,
    weight DECIMAL(10,2),
    FOREIGN KEY (shipment_id) REFERENCES shipments(id)
)";
$conn->query($sql);

// Create activity_log table
$sql = "CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($sql);


