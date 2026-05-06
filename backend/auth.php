<?php
// Authentication API - Handles Login & Register for User, Store, Admin
session_start();
require_once 'config.php';

// Allow CORS and set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST or GET
if($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Use POST or GET.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$conn = getDBConnection();

switch($action) {
    // ===================== LOGIN =====================
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if(empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password required']);
            exit;
        }
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && password_verify($password, $user['password'])) {
            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // If store, get store info
            if($user['role'] == 'store') {
                $storeStmt = $conn->prepare("SELECT * FROM stores WHERE user_id = ?");
                $storeStmt->execute([$user['id']]);
                $store = $storeStmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['store_id'] = $store['id'] ?? null;
                $_SESSION['store_status'] = $store['status'] ?? 'pending';
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'phone' => $user['phone'],
                    'address' => $user['address']
                ],
                'redirect' => getDashboardUrl($user['role'])
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
        break;
        
    // ===================== REGISTER USER =====================
    case 'register_user':
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        
        if(empty($username) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone, address, status) VALUES (?, ?, ?, 'user', ?, ?, 'active')");
        if($stmt->execute([$username, $email, $hashedPassword, $phone, $address])) {
            $userId = $conn->lastInsertId();
            
            // Auto login after register
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'user';
            
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'redirect' => 'index.html'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
        break;
        
    // ===================== REGISTER STORE =====================
    case 'register_store':
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $storeName = $_POST['store_name'] ?? '';
        $storeDescription = $_POST['store_description'] ?? '';
        $businessAddress = $_POST['business_address'] ?? '';
        $gstNumber = $_POST['gst_number'] ?? '';
        
        if(empty($username) || empty($email) || empty($password) || empty($storeName)) {
            echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
            exit;
        }
        
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already registered']);
            exit;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $conn->beginTransaction();
            
            // Create user with store role
            $userStmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone, status) VALUES (?, ?, ?, 'store', ?, 'active')");
            $userStmt->execute([$username, $email, $hashedPassword, $phone]);
            $userId = $conn->lastInsertId();
            
            // Create store record (pending approval)
            $storeStmt = $conn->prepare("INSERT INTO stores (user_id, store_name, store_description, business_address, gst_number, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $storeStmt->execute([$userId, $storeName, $storeDescription, $businessAddress, $gstNumber]);
            $storeId = $conn->lastInsertId();
            
            $conn->commit();
            
            // Auto login after register
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'store';
            $_SESSION['store_id'] = $storeId;
            $_SESSION['store_status'] = 'pending';
            
            echo json_encode([
                'success' => true,
                'message' => 'Store registration successful! Waiting for admin approval.',
                'redirect' => 'store-dashboard.html'
            ]);
        } catch(Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
        }
        break;
        
    // ===================== LOGOUT =====================
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        break;
        
    // ===================== CHECK SESSION =====================
    case 'check_session':
        if(isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'email' => $_SESSION['email'],
                    'role' => $_SESSION['role']
                ]
            ]);
        } else {
            echo json_encode(['success' => true, 'logged_in' => false]);
        }
        break;
        
    // ===================== GET USER DASHBOARD DATA =====================
    case 'get_user_dashboard':
        requireAuth('user');
        $userId = $_SESSION['user_id'];
        
        // Get user orders
        $ordersStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
        $ordersStmt->execute([$userId]);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get order count
        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
        $countStmt->execute([$userId]);
        $orderCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode([
            'success' => true,
            'orders' => $orders,
            'total_orders' => $orderCount
        ]);
        break;
        
    // ===================== GET STORE DASHBOARD DATA =====================
    case 'get_store_dashboard':
        requireAuth('store');
        $storeId = $_SESSION['store_id'];
        
        // Get store products
        $productsStmt = $conn->prepare("SELECT * FROM products WHERE store_id = ? ORDER BY created_at DESC");
        $productsStmt->execute([$storeId]);
        $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get store orders (orders containing store's products)
        $ordersStmt = $conn->prepare("
            SELECT DISTINCT o.* FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            JOIN products p ON oi.product_id = p.id 
            WHERE p.store_id = ? 
            ORDER BY o.created_at DESC
        ");
        $ordersStmt->execute([$storeId]);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get stats
        $totalProducts = count($products);
        $totalOrders = count($orders);
        
        echo json_encode([
            'success' => true,
            'products' => $products,
            'orders' => $orders,
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'store_status' => $_SESSION['store_status']
        ]);
        break;
        
    // ===================== GET ADMIN DASHBOARD DATA =====================
    case 'get_admin_dashboard':
        requireAuth('admin');
        
        // Get all users
        $usersStmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all stores
        $storesStmt = $conn->query("SELECT s.*, u.username, u.email FROM stores s JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC");
        $stores = $storesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all orders
        $ordersStmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 50");
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get stats
        $stats = [
            'total_users' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
            'total_stores' => $conn->query("SELECT COUNT(*) FROM stores WHERE status = 'approved'")->fetchColumn(),
            'pending_stores' => $conn->query("SELECT COUNT(*) FROM stores WHERE status = 'pending'")->fetchColumn(),
            'total_orders' => $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'total_revenue' => $conn->query("SELECT SUM(total_amount) FROM orders")->fetchColumn() ?? 0
        ];
        
        echo json_encode([
            'success' => true,
            'users' => $users,
            'stores' => $stores,
            'orders' => $orders,
            'stats' => $stats
        ]);
        break;
        
    // ===================== APPROVE/REJECT STORE =====================
    case 'update_store_status':
        requireAuth('admin');
        $storeId = $_POST['store_id'] ?? 0;
        $status = $_POST['status'] ?? '';
        
        if(!in_array($status, ['approved', 'rejected'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }
        
        $stmt = $conn->prepare("UPDATE stores SET status = ? WHERE id = ?");
        if($stmt->execute([$status, $storeId])) {
            echo json_encode(['success' => true, 'message' => 'Store status updated to ' . $status]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update store status']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

// Helper functions
function getDashboardUrl($role) {
    switch($role) {
        case 'admin': return 'admin-dashboard.html';
        case 'store': return 'store-dashboard.html';
        case 'user': return 'user-dashboard.html';
        default: return 'index.html';
    }
}

function requireAuth($requiredRole = null) {
    if(!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first', 'redirect' => 'login.html']);
        exit;
    }
    
    if($requiredRole && $_SESSION['role'] != $requiredRole) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
}

$conn = null;
?>
