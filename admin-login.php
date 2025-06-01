<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ShopX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            font-weight: 600;
        }
        .form-control {
            padding: 12px;
            border-radius: 5px;
        }
        .btn-login {
            padding: 12px;
            font-weight: 600;
            background-color: #007bff;
            border: none;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: #dc3545;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Admin Login</h2>
                <p class="text-muted">Enter your credentials to access the admin dashboard</p>
            </div>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            <form action="admin-auth.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100">Login</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 