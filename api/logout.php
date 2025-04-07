<?php
header('Content-Type: application/json');
session_start();

// Initialize response
$response = [
    'success' => false,
    'message' => 'Logout failed',
    'cart' => []
];

try {
    // Preserve cart data before destroying session
    if (isset($_SESSION['cart'])) {
        $response['cart'] = array_map(function($item) {
            return [
                'product_id' => $item['id'] ?? null,
                'name' => $item['name'] ?? 'Unknown Product',
                'price' => $item['price'] ?? 0,
                'quantity' => $item['quantity'] ?? 1
            ];
        }, $_SESSION['cart']);
    }

    // Clear authentication data
    $_SESSION = [];
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    // Update response
    $response = [
        'success' => true,
        'message' => 'Logged out successfully',
        'cart' => $response['cart'] // Preserve the formatted cart
    ];

} catch (Exception $e) {
    $response['message'] = 'Error during logout: ' . $e->getMessage();
}

// Ensure no output before this
ob_clean();
echo json_encode($response);
exit;