<?php
header('Content-Type: application/json');
require_once '../db.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['id']) || empty($data['name']) || !isset($data['price'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

try {
    // Update the product
    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ? WHERE id = ?");
    $success = $stmt->execute([
        $data['name'],
        $data['price'],
        $data['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update product');
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
