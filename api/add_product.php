<?php
// Turn off error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Ensure no output before headers
if (ob_get_level()) ob_clean();

header('Content-Type: application/json');
require_once '../db.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Convert price to proper decimal format
    $price = is_numeric($data['price']) ? $data['price'] : 0;
    
    $stmt = $pdo->prepare("INSERT INTO products (name, price, image_url, image_path) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([
        $data['name'],
        $price,
        $data['image_url'] ?? '',
        $data['image_path'] ?? ''
    ]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Product added successfully'
        ]);
    } else {
        throw new Exception('Failed to insert product');
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
