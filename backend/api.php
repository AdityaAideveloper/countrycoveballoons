<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$resource = $request[0] ?? '';

switch ($resource) {
    case 'products':
        handleProducts($method, $request);
        break;
    case 'orders':
        handleOrders($method, $request);
        break;
    case 'users':
        handleUsers($method, $request);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
        break;
}

function handleProducts($method, $request) {
    $conn = getDBConnection();

    switch ($method) {
        case 'GET':
            if (isset($request[1])) {
                // Get single product
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$request[1]]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($product);
            } else {
                // Get all products with optional filters
                $where = [];
                $params = [];

                if (isset($_GET['category']) && $_GET['category'] !== 'all') {
                    $where[] = "category = ?";
                    $params[] = $_GET['category'];
                }

                if (isset($_GET['color']) && $_GET['color'] !== 'all') {
                    $where[] = "color = ?";
                    $params[] = $_GET['color'];
                }

                if (isset($_GET['price_range']) && $_GET['price_range'] !== 'all') {
                    $range = explode('-', $_GET['price_range']);
                    if (count($range) === 2) {
                        $where[] = "price BETWEEN ? AND ?";
                        $params[] = $range[0];
                        $params[] = $range[1];
                    } elseif ($range[0] === '1000') {
                        $where[] = "price >= ?";
                        $params[] = $range[0];
                    }
                }

                $sql = "SELECT * FROM products";
                if (!empty($where)) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }

                $stmt = $conn->prepare($sql);
                $stmt->execute($params);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($products);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, color, image, rating) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['description'],
                $data['price'],
                $data['category'],
                $data['color'],
                $data['image'],
                $data['rating'] ?? 0
            ]);
            echo json_encode(['id' => $conn->lastInsertId()]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }

    $conn = null;
}

function handleOrders($method, $request) {
    $conn = getDBConnection();

    switch ($method) {
        case 'GET':
            if (isset($request[1])) {
                // Get single order
                $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
                $stmt->execute([$request[1]]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($order);
            } else {
                // Get all orders
                $stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($orders);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            // Start transaction
            $conn->beginTransaction();

            try {
                // Insert order
                $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, customer_city, customer_pincode, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data['customer_name'],
                    $data['customer_email'],
                    $data['customer_phone'],
                    $data['customer_address'],
                    $data['customer_city'],
                    $data['customer_pincode'],
                    $data['total_amount']
                ]);
                $orderId = $conn->lastInsertId();

                // Insert order items
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                foreach ($data['items'] as $item) {
                    $stmt->execute([
                        $orderId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['price']
                    ]);
                }

                $conn->commit();
                echo json_encode(['order_id' => $orderId, 'message' => 'Order placed successfully']);
            } catch (Exception $e) {
                $conn->rollBack();
                http_response_code(500);
                echo json_encode(['error' => 'Failed to place order']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }

    $conn = null;
}

function handleUsers($method, $request) {
    $conn = getDBConnection();

    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data['action'])) {
                if ($data['action'] === 'register') {
                    // Register user
                    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    try {
                        $stmt->execute([$data['username'], $data['email'], $hashedPassword]);
                        echo json_encode(['message' => 'User registered successfully']);
                    } catch (PDOException $e) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Username or email already exists']);
                    }
                } elseif ($data['action'] === 'login') {
                    // Login user
                    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$data['username'], $data['username']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && password_verify($data['password'], $user['password'])) {
                        // Start session
                        session_start();
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        echo json_encode(['message' => 'Login successful', 'user' => ['id' => $user['id'], 'username' => $user['username']]]);
                    } else {
                        http_response_code(401);
                        echo json_encode(['error' => 'Invalid credentials']);
                    }
                }
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }

    $conn = null;
}
?>