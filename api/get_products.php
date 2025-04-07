<?php
// Turn off error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Ensure no output before headers
if (ob_get_level()) ob_clean();

header('Content-Type: application/json');
require_once '../db.php';

try {
    $stmt = $pdo->query("SELECT id, name, CAST(price AS DECIMAL(10,2)) as price, image_url, image_path FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
