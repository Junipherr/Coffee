<?php
// Turn off error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Ensure no output before headers
if (ob_get_level()) ob_clean();

header('Content-Type: application/json');
require_once '../db.php';

// Debugging: Log received files
error_log(print_r($_FILES, true));

$uploadDir = '../uploads/products/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        die(json_encode(['success' => false, 'message' => 'Failed to create upload directory']));
    }
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];
$maxSize = 2 * 1024 * 1024; // 2MB

try {
    if (empty($_FILES['image'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['image'];
    
    // Validate file type
    if (!array_key_exists($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and WEBP are allowed.');
    }
    
    // Validate file size
    if ($file['size'] > $maxSize) {
        throw new Exception('File too large. Maximum size is 2MB.');
    }

    // Generate unique filename
    $extension = $allowedTypes[$file['type']];
    $filename = uniqid('prod_') . '.' . $extension;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode([
            'success' => true,
            'path' => 'uploads/products/' . $filename
        ]);
    } else {
        throw new Exception('Failed to save uploaded file');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>