<?php
require_once 'functions.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($mobile)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = 'Please enter a valid 10-digit mobile number';
    } else {
        $result = registerUser($name, $email, $password, $mobile);
        if ($result === true) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ShopX</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #FF1493, #ff0084);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        .auth-container {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .auth-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .auth-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .auth-form {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }

        .form-group input:focus {
            border-color: #FF1493;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 20, 147, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: #FF1493;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: #ff0084;
            transform: translateY(-2px);
        }

        .auth-links {
            text-align: center;
            margin-top: 25px;
            color: white;
        }

        .auth-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border-bottom: 2px solid white;
            padding-bottom: 2px;
        }

        .auth-link:hover {
            opacity: 0.8;
        }

        .message {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .message.error {
            background: rgba(255, 230, 230, 0.95);
            color: #d63031;
            border: 2px solid #fab1a0;
        }

        @media (min-width: 768px) {
            .auth-container {
                padding: 40px;
            }

            .auth-title {
                font-size: 40px;
            }

            .auth-subtitle {
                font-size: 18px;
            }

            .auth-form {
                padding: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="auth-header">
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join ShopX today</p>
        </div>

        <form action="signup.php" method="post" class="auth-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required 
                       placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="tel" id="mobile" name="mobile" required 
                       placeholder="Enter your 10-digit mobile number"
                       pattern="[0-9]{10}"
                       maxlength="10">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter your password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       placeholder="Confirm your password">
            </div>

            <button type="submit" class="submit-btn">
                <i class="material-icons">person_add</i>
                Sign Up
            </button>
        </form>

        <div class="auth-links">
            <p>Already have an account? <a href="login.php" class="auth-link">Login</a></p>
        </div>
    </div>
</body>
</html> 