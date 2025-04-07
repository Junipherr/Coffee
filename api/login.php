<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure no output before headers
if (ob_get_level()) ob_clean();

header('Content-Type: application/json');
require_once '../db.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];
$password = $data['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    // Initialize empty cart array
    $dbCart = [];
    
    try {
        // Check if user_cart table exists
        $tableExists = $pdo->query("SHOW TABLES LIKE 'user_cart'")->rowCount() > 0;
        
        if ($tableExists) {
            // Get any existing cart items from database
            $stmt = $pdo->prepare("
                SELECT product_id, quantity 
                FROM user_cart 
                WHERE user_id = ?
            ");
            $stmt->execute([$user['id']]);
            $dbCart = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        // Silently fail if cart table doesn't exist or query fails
        $dbCart = [];
    }

    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ],
        'role' => $user['role'],
        'cart' => $dbCart
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
