<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 pt-5">
        <h2 class="text-center">Login</h2>
        <form id="loginForm" onsubmit="return handleLogin(event)">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <script>
        async function handleLogin(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
        
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                
                // First check if response is JSON
                const responseText = await response.text();
                let result;
                
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    // If not JSON, show the raw response (likely PHP error)
                    throw new Error(responseText || 'Invalid server response');
                }

                if (!response.ok) {
                    throw new Error(result.message || 'Login failed');
                }
                
                if (result.success) {
                    localStorage.setItem('user', JSON.stringify(result.user));
                    localStorage.setItem('loggedIn', 'true');
                    
                    // Handle cart data if returned
                    if (result.cart) {
                        const serverCart = result.cart.map(item => ({
                            id: item.product_id,
                            quantity: item.quantity
                        }));
                        localStorage.setItem('cart', JSON.stringify(serverCart));
                    }
                    
                    // Redirect based on user role
                    switch(result.user.role) {
                        case 'admin':
                            window.location.href = 'admin.php';
                            break;
                        case 'customer':
                            window.location.href = 'customer.php';
                            break;
                        default:
                            window.location.href = 'start.html'; // Default page for other roles
                    }
                } else {
                    alert(result.message || 'Login failed');
                }
            } catch (error) {
                console.error('Login error:', error);
                alert(`Login error: ${error.message}`);
            }
        }
    </script>
</body>

</html>