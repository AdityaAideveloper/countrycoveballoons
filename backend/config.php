<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'balloon_planet');

// Create database connection
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Initialize database tables
function initializeDatabase() {
    $conn = getDBConnection();

    // Create users table with roles
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'store', 'admin') DEFAULT 'user',
        phone VARCHAR(20),
        address TEXT,
        status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);

    // Create stores table for sellers
    $sql = "CREATE TABLE IF NOT EXISTS stores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        store_name VARCHAR(100) NOT NULL,
        store_description TEXT,
        store_logo VARCHAR(255),
        business_address TEXT,
        gst_number VARCHAR(50),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);

    // Create products table with store reference
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INT DEFAULT 100,
        category VARCHAR(50) NOT NULL,
        color VARCHAR(50) NOT NULL,
        image VARCHAR(255),
        rating DECIMAL(3,1) DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL
    )";
    $conn->exec($sql);

    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        customer_address TEXT NOT NULL,
        customer_city VARCHAR(100) NOT NULL,
        customer_pincode VARCHAR(10) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->exec($sql);

    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    $conn->exec($sql);

    // Insert sample products if table is empty
    $result = $conn->query("SELECT COUNT(*) FROM products");
    if ($result->fetchColumn() == 0) {
        $sampleProducts = [
            ['Rainbow Birthday Balloon Set', 'Beautiful rainbow colored balloons perfect for birthday celebrations. Includes 20 balloons in various sizes.', 599.00, 'birthday', 'multi', 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=400&q=80', 4.5],
            ['Golden Wedding Balloons', 'Elegant gold balloons for wedding decorations. Premium quality latex balloons with heart shapes.', 899.00, 'wedding', 'gold', 'https://images.unsplash.com/photo-1504196606672-aef5c9cefc92?w=400&q=80', 4.8],
            ['Pink Anniversary Package', 'Romantic pink balloon arrangement perfect for anniversary celebrations. Includes ribbons and weights.', 749.00, 'anniversary', 'pink', 'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=400&q=80', 4.7],
            ['Blue Party Balloon Kit', 'Complete blue balloon kit for boys\' birthday parties. Includes balloons, pump, and decorations.', 449.00, 'birthday', 'blue', 'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?w=400&q=80', 4.3],
            ['Red Heart Balloons', 'Red heart-shaped balloons for Valentine\'s Day and romantic occasions.', 399.00, 'anniversary', 'red', 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400&q=80', 4.6],
            ['Silver Celebration Set', 'Silver metallic balloons for elegant celebrations and corporate events.', 699.00, 'wedding', 'silver', 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=400&q=80', 4.4]
        ];

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, color, image, rating) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($sampleProducts as $product) {
            $stmt->execute($product);
        }
    }
    
    // Create default admin user if no admin exists
    $adminCheck = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ($adminCheck->fetchColumn() == 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone, status) VALUES (?, ?, ?, 'admin', ?, 'active')");
        $stmt->execute(['Admin', 'admin@countrycoveballoons.com', $adminPassword, '9876543210']);
    }

    $conn = null;
}

// Call initialize function
initializeDatabase();
?>