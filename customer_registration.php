<?php
require_once 'db.php';
require_once 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $errors = [];
    $name = filter_input(INPUT_POST, 'customerName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'customerEmail', FILTER_SANITIZE_EMAIL);
    $password = $_POST['customerPassword'];
    $confirmPassword = $_POST['customerConfirmPassword'];
    $phone = filter_input(INPUT_POST, 'customerPhone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'customerAddress', FILTER_SANITIZE_STRING);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Check password match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }

    // Check password strength
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Invalid phone number format";
    }

    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already registered";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert customer
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role, name, phone, address, is_approved) 
                                     VALUES (?, ?, 'customer', ?, ?, ?, TRUE)");
                $stmt->execute([$email, $hashedPassword, $name, $phone, $address]);
                
                $_SESSION['success_message'] = "Registration successful! You can now login.";
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4">Customer Registration</h2>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="customerName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="customerName" name="customerName" 
                                       value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="customerEmail" name="customerEmail"
                                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customerPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="customerPassword" name="customerPassword" required>
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customerConfirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="customerConfirmPassword" name="customerConfirmPassword" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customerPhone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="customerPhone" name="customerPhone"
                                       value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>" required>
                                <small class="form-text text-muted">Format: 10-15 digits</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customerAddress" class="form-label">Address</label>
                                <textarea class="form-control" id="customerAddress" name="customerAddress" required><?= isset($address) ? htmlspecialchars($address) : '' ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <p>Admin? <a href="admin_registration.php">Register here</a></p>
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
