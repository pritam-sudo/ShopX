<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'functions.php';

// Check if user is logged in
session_start();
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user details
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

if (!$user) {
    header('Location: login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validate required fields
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($mobile)) $errors[] = "Mobile number is required";

    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate mobile format (10 digits)
    if (!empty($mobile) && !preg_match('/^[0-9]{10}$/', $mobile)) {
        $errors[] = "Mobile number must be 10 digits";
    }

    // Handle password change if requested
    if (!empty($current_password)) {
        if (empty($new_password)) {
            $errors[] = "New password is required when changing password";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        } elseif (!verifyPassword($user_id, $current_password)) {
            $errors[] = "Current password is incorrect";
        }
    }

    if (empty($errors)) {
        try {
            // Update user profile
            if (updateUserProfile($user_id, $name, $email, $mobile)) {
                // Update password if provided
                if (!empty($new_password)) {
                    if (!updateUserPassword($user_id, $new_password)) {
                        $errors[] = "Failed to update password";
                    }
                }
                
                if (empty($errors)) {
                    $_SESSION['success_message'] = "Profile updated successfully!";
                    header('Location: profile.php');
                    exit;
                }
            } else {
                $errors[] = "Failed to update profile. Email might already be in use.";
            }
        } catch (Exception $e) {
            $errors[] = "An error occurred while updating the profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - ShopX</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF1493;
            --background-color: #FFF8E7;
            --text-color: #333;
            --border-color: #eee;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
            padding-bottom: 80px;
        }

        .header {
            background-color: var(--background-color);
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .edit-profile-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .profile-form {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-secondary {
            background: #f5f5f5;
            color: var(--text-color);
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1 class="page-title">Edit Profile</h1>
            <a href="profile.php" class="btn btn-secondary">
                <i class="material-icons">arrow_back</i>
                Back
            </a>
        </div>
    </div>

    <div class="edit-profile-container">
        <?php if (!empty($errors)): ?>
            <div class="alert">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="profile-form" method="POST">
            <div class="form-section">
                <h2 class="section-title">Personal Information</h2>
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input" required 
                           value="<?php echo htmlspecialchars($user['name']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" required 
                           value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile Number</label>
                    <input type="tel" name="mobile" class="form-input" required 
                           value="<?php echo htmlspecialchars($user['mobile']); ?>" 
                           pattern="[0-9]{10}">
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">Change Password</h2>
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-input" 
                           minlength="6">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="material-icons">save</i>
                    Save Changes
                </button>
                <a href="profile.php" class="btn btn-secondary">
                    <i class="material-icons">close</i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html> 