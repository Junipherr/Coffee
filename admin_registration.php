<?php
require_once 'db.php';
require_once 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $errors = [];
    $name = filter_input(INPUT_POST, 'adminName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'adminEmail', FILTER_SANITIZE_EMAIL);
    $password = $_POST['adminPassword'];
    $confirmPassword = $_POST['adminConfirmPassword'];
    $adminCode = $_POST['adminCode'];
    
    // Verify admin registration code
    if ($adminCode !== ADMIN_REGISTRATION_CODE) {
        $errors[] = "Invalid admin registration code";
    }

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
                
                // Insert admin
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role, name, is_approved) 
                                     VALUES (?, ?, 'admin', ?, TRUE)");
                $stmt->execute([$email, $hashedPassword, $name]);
                
                $_SESSION['success_message'] = "Admin registration successful!";
                header("Location: login.php?admin_registration=success");
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
    <title>Admin Registration | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4">Admin Registration</h2>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="adminName" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="adminName" name="adminName" 
                                       value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adminEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="adminEmail" name="adminEmail"
                                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adminPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adminConfirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="adminConfirmPassword" name="adminConfirmPassword" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adminCode" class="form-label">Admin Registration Code</label>
                                <input type="password" class="form-control" id="adminCode" name="adminCode" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
