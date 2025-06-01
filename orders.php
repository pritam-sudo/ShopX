<?php
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user details and orders
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);
$orders = getUserOrders($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders - ShopX</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF1493;
            --background-color: #FFF8E7;
            --text-color: #333;
            --border-color: #eee;
            --success-color: #4CAF50;
            --warning-color: #FFC107;
            --danger-color: #f44336;
            --info-color: #2196F3;
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

        .orders-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .order-info {
            flex: 1;
        }

        .order-number {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-color);
            margin: 0 0 5px 0;
        }

        .order-date {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .order-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }

        .status-delivered {
            background-color: #E8F5E9;
            color: var(--success-color);
        }

        .status-processing {
            background-color: #FFF3E0;
            color: var(--warning-color);
        }

        .status-cancelled {
            background-color: #FFEBEE;
            color: var(--danger-color);
        }

        .status-shipping {
            background-color: #E3F2FD;
            color: var(--info-color);
        }

        .order-items {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .order-item {
            min-width: 60px;
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
        }

        .order-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .order-total {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .order-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 48px;
            color: #666;
            margin-bottom: 20px;
        }

        .empty-state-text {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 15px;
            border-top: 1px solid var(--border-color);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--text-color);
            font-size: 12px;
        }

        .nav-item.active {
            color: var(--primary-color);
        }

        .nav-icon {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                gap: 10px;
            }

            .order-status {
                align-self: flex-start;
            }

            .order-actions {
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
            <h1 class="page-title">Your Orders</h1>
            <a href="profile.php" class="btn btn-secondary">
                <i class="material-icons">arrow_back</i>
                Back
            </a>
        </div>
    </div>

    <div class="orders-container">
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="material-icons empty-state-icon">shopping_bag</i>
                <p class="empty-state-text">You haven't placed any orders yet</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="material-icons">shopping_cart</i>
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <p class="order-number">Order #<?php echo $order['id']; ?></p>
                            <p class="order-date"><?php echo date('d M Y', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </div>
                    </div>

                    <div class="order-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-total">
                        Total: ₹<?php echo number_format($order['total_amount'], 2); ?>
                    </div>

                    <div class="order-actions">
                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-primary">
                            <i class="material-icons">visibility</i>
                            View Details
                        </a>
                        <?php if ($order['status'] === 'delivered'): ?>
                            <a href="write-review.php?order_id=<?php echo $order['id']; ?>" class="btn btn-secondary">
                                <i class="material-icons">rate_review</i>
                                Write Review
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="material-icons nav-icon">home</i>
            <span>Home</span>
        </a>
        <a href="wishlist.php" class="nav-item">
            <i class="material-icons nav-icon">favorite_border</i>
            <span>Wishlist</span>
        </a>
        <a href="cart.php" class="nav-item" style="position: relative;">
            <i class="material-icons nav-icon">shopping_cart</i>
            <span class="cart-badge">14</span>
            <span>Cart</span>
        </a>
        <a href="orders.php" class="nav-item active">
            <i class="material-icons nav-icon">receipt</i>
            <span>Orders</span>
        </a>
        <a href="profile.php" class="nav-item">
            <i class="material-icons nav-icon">person</i>
            <span>Profile</span>
        </a>
    </nav>
</body>
</html> 