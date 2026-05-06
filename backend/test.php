<?php
// Database connection test
header('Content-Type: application/json');

try {
    // Test MySQL connection
    $conn = new PDO("mysql:host=localhost", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $conn->query("SHOW DATABASES LIKE 'balloon_planet'");
    $dbExists = $stmt->fetch() ? true : false;
    
    if(!$dbExists) {
        // Create database
        $conn->exec("CREATE DATABASE IF NOT EXISTS balloon_planet");
    }
    
    // Connect to the database
    $conn->exec("USE balloon_planet");
    
    // Check if tables exist
    $tables = [];
    $stmt = $conn->query("SHOW TABLES");
    while($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connected successfully!',
        'database_exists' => $dbExists,
        'tables' => $tables,
        'tables_count' => count($tables),
        'ready' => count($tables) >= 5
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed!',
        'error' => $e->getMessage(),
        'solution' => '1. Start XAMPP Control Panel\n2. Start Apache and MySQL\n3. Refresh this page'
    ]);
}
?>
