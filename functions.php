<?php
session_start();

// Database connection configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'shopdb';

// Create database connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Connection failed. Please try again later.");
}

// Set charset to ensure proper handling of special characters
mysqli_set_charset($conn, "utf8mb4");

function addProduct($name, $description, $price, $image_url, $category) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "INSERT INTO products (name, description, price, image_url, category) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssdss", $name, $description, $price, $image_url, $category);
    
    if (mysqli_stmt_execute($stmt)) {
        $product_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $product_id;
    }
    mysqli_stmt_close($stmt);
    return false;
}

function getProducts() {
    global $conn;
    
    $query = "SELECT * FROM products ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Query failed: " . mysqli_error($conn));
        return array();
    }
    
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return $products;
}

function getProductById($id) {
    global $conn;
    
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    return $product;
}

function deleteProduct($id) {
    global $conn;
    
    $id = (int)$id;
    
    // Get product image before deleting
    $stmt = mysqli_prepare($conn, "SELECT image_url FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    // Delete from database
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);
    
    // If deletion was successful and there was an image, delete it
    if ($success && $product && !empty($product['image_url'])) {
        $image_path = $product['image_url'];
        if (file_exists($image_path) && $image_path !== 'uploads/default-product.jpg') {
            unlink($image_path);
        }
    }
    
    mysqli_stmt_close($stmt);
    return $success;
}

function updateProduct($id, $name, $description, $price, $image_url, $category) {
    global $conn;
    
    $id = (int)$id;
    
    if ($image_url) {
        $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssdssi", $name, $description, $price, $image_url, $category, $id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, description = ?, price = ?, category = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssdsi", $name, $description, $price, $category, $id);
    }
    
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Function to validate image file
function isValidImage($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }
    
    $image_info = getimagesize($file_path);
    return $image_info !== false;
}

function getProductsByCategory($category) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $products = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $products;
}

// User Authentication Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function loginUser($mobile, $password) {
    global $conn;
    
    // Validate input
    if (empty($mobile) || empty($password)) {
        error_log("Login attempt with empty fields");
        return false;
    }
    
    // Validate mobile number format
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        error_log("Invalid mobile number format: $mobile");
        return false;
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE mobile = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $mobile);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log("Invalid password for user: $mobile");
        }
    } else {
        error_log("User not found: $mobile");
    }
    
    mysqli_stmt_close($stmt);
    return false;
}

function registerUser($name, $email, $password, $mobile) {
    global $conn;
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($mobile)) {
        error_log("Registration attempt with empty fields");
        return 'All fields are required';
    }
    
    // Validate mobile number format
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        error_log("Invalid mobile number format: $mobile");
        return 'Please enter a valid 10-digit mobile number';
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email format: $email");
        return 'Please enter a valid email address';
    }
    
    // Validate password strength
    if (strlen($password) < 6) {
        error_log("Password too short");
        return 'Password must be at least 6 characters long';
    }
    
    // Check if email already exists
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_stmt_close($stmt);
        error_log("Email already exists: $email");
        return 'Email already registered';
    }
    
    // Check if mobile number already exists
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE mobile = ?");
    mysqli_stmt_bind_param($stmt, "s", $mobile);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_stmt_close($stmt);
        error_log("Mobile number already exists: $mobile");
        return 'Mobile number already registered';
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, mobile) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $mobile);
    
    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return true;
    }
    
    $error = mysqli_error($conn);
    error_log("Registration failed: " . $error);
    mysqli_stmt_close($stmt);
    return 'Registration failed. Please try again.';
}

function logoutUser() {
    session_destroy();
    header('Location: index.php');
    exit;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getUserById($id) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, mobile FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    return $user;
}

function createOrder($user_id, $total_amount, $address, $payment_method) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, address, payment_method, status) VALUES (?, ?, ?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, "idss", $user_id, $total_amount, $address, $payment_method);
    
    if (mysqli_stmt_execute($stmt)) {
        $order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $order_id;
    }
    
    mysqli_stmt_close($stmt);
    return false;
}

function addOrderItem($order_id, $product_id, $quantity, $price) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiid", $order_id, $product_id, $quantity, $price);
    
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

function getOrderById($order_id) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    return $order;
}

function getOrderItems($order_id) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "SELECT oi.*, p.name, p.image_url FROM order_items oi 
                                  JOIN products p ON oi.product_id = p.id 
                                  WHERE oi.order_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $items = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $items;
}

function getUserOrders($user_id) {
    global $conn;
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $orders = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $orders;
}

function getTotalOrders() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM orders";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getTotalProducts() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM products";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getTotalCustomers() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM users";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function getTotalRevenue() {
    global $conn;
    $query = "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function getRecentOrders($limit = 10) {
    global $conn;
    $query = "SELECT o.*, u.name as customer_name 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              ORDER BY o.created_at DESC 
              LIMIT ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $orders;
}

function updateUserProfile($user_id, $name, $email, $mobile) {
    global $conn;
    
    // Check if email already exists for another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return false; // Email already exists
    }
    
    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, mobile = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $mobile, $user_id);
    
    return $stmt->execute();
}

function updateUserPassword($user_id, $new_password) {
    global $conn;
    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    return $stmt->execute();
}

function verifyPassword($user_id, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return password_verify($password, $row['password']);
    }
    
    return false;
}
?> 