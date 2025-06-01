<?php
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle logout
if (isset($_POST['logout'])) {
    logoutUser();
    header('Location: login.php');
    exit;
}

// Get user details
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

// Get user's orders
$orders = getUserOrders($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopX - Profile</title>
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
        }

        .header {
            background-color: #FF1493;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color:rgb(255, 255, 255);
            text-decoration: none;
        }

        .header-icons {
            display: flex;
            gap: 20px;
        }

        .header-icons a {
            color: rgb(255, 255, 255);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .header-icons i {
            font-size: 24px;
        }

        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .user-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-greeting {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .action-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--text-color);
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .action-card:hover {
            transform: translateY(-2px);
        }

        .settings-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .settings-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .settings-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .settings-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
            text-decoration: none;
            color: var(--text-color);
        }

        .settings-item:last-child {
            border-bottom: none;
        }

        .settings-item-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .settings-icon {
            width: 24px;
            height: 24px;
            color: var(--text-color);
        }

        .settings-label {
            font-size: 16px;
            font-weight: 500;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }

        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #666;
            font-size: 12px;
            gap: 5px;
        }

        .bottom-nav a.active {
            color: #FF1493;
        }

        .bottom-nav i {
            font-size: 24px;
        }

        @media (max-width: 768px) {
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">ShopX</div>
        <div class="header-icons">
            <a href="cart.php" class="cart-icon">
                <i class="material-icons">shopping_cart</i>
            </a>
            <a href="profile.php" class="profile-icon">
                <i class="material-icons">person</i>
            </a>
        </div>
    </header>

    <div class="profile-container">
        <div class="user-header">
            
            <h1 class="user-greeting">Hello, <?php echo htmlspecialchars($user['name']); ?></h1>
        </div>

        <div class="quick-actions">
            <a href="orders.php" class="action-card">
                <h3>Your Order</h3>
            </a>
            
        </div>

        <div class="settings-section">
            <h2 class="settings-header">Account Settings</h2>
            <ul class="settings-list">
                <a href="edit-profile.php" class="settings-item">
                    <div class="settings-item-left">
                        <i class="material-icons settings-icon">person</i>
                        <span class="settings-label">Edit Profile</span>
                    </div>
                    <i class="material-icons">chevron_right</i>
                </a>
                <a href="saved-cards.php" class="settings-item">
                    <div class="settings-item-left">
                        <i class="material-icons settings-icon">credit_card</i>
                        <span class="settings-label">Saved Cards & Wallet</span>
                    </div>
                    <i class="material-icons">chevron_right</i>
                </a>
                <a href="addresses.php" class="settings-item">
                    <div class="settings-item-left">
                        <i class="material-icons settings-icon">location_on</i>
                        <span class="settings-label">Saved Addresses</span>
                    </div>
                    <i class="material-icons">chevron_right</i>
                </a>
                <a href="language.php" class="settings-item">
                    <div class="settings-item-left">
                        <i class="material-icons settings-icon">language</i>
                        <span class="settings-label">Select Language</span>
                    </div>
                    <i class="material-icons">chevron_right</i>
                </a>
                <a href="notifications.php" class="settings-item">
                    <div class="settings-item-left">
                        <i class="material-icons settings-icon">notifications</i>
                        <span class="settings-label">Notifications Settings</span>
                    </div>
                    <i class="material-icons">chevron_right</i>
                </a>
                <form method="POST" style="margin: 0;">
                    <button type="submit" name="logout" class="settings-item" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left; padding: 15px 0;">
                        <div class="settings-item-left">
                            <i class="material-icons settings-icon" style="color: #f44336;">logout</i>
                            <span class="settings-label" style="color: #f44336;">Logout</span>
                        </div>
                        <i class="material-icons" style="color: #f44336;">chevron_right</i>
                    </button>
                </form>
            </ul>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="index.php">
            <i class="material-icons">home</i>
            <span>Home</span>
        </a>
        <a href="categories.php">
            <i class="material-icons">grid_view</i>
            <span>Categories</span>
        </a>
        <a href="shop.php">
            <i class="material-icons">shopping_cart</i>
            <span>Shop</span>
        </a>
        <a href="profile.php" class="active">
            <i class="material-icons">person</i>
            <span>Profile</span>
        </a>
    </nav>

    <script>
        // Add any necessary JavaScript functionality here
    </script>
</body>
</html> 