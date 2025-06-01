<?php
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$order_id) {
    header('Location: orders.php');
    exit;
}

// Get order details
$order = getOrderById($order_id);
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: orders.php');
    exit;
}

// Get order items
$items = getOrderItems($order_id);

// Get user details
$user = getUserById($order['user_id']);

// Define status colors
$status_colors = [
    'pending' => '#FFC107',    // Warning yellow
    'processing' => '#2196F3',  // Info blue
    'shipped' => '#FF1493',     // Primary pink
    'delivered' => '#4CAF50',   // Success green
    'cancelled' => '#f44336'    // Danger red
];

$status_color = $status_colors[$order['status']] ?? '#666666';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - ShopX</title>
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

        .order-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .order-info h2 {
            font-size: 18px;
            margin: 0 0 5px 0;
        }

        .order-date {
            color: #666;
            font-size: 14px;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            text-transform: capitalize;
        }

        .delivery-info {
            margin-bottom: 25px;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
        }

        .items-list {
            margin-bottom: 25px;
        }

        .item-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 15px;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 500;
            margin: 0 0 5px 0;
        }

        .item-price {
            color: var(--primary-color);
            font-weight: 500;
        }

        .item-quantity {
            color: #666;
            font-size: 14px;
        }

        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
            font-weight: 600;
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

        .btn-secondary {
            background: #f5f5f5;
            color: var(--text-color);
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 15px 0;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                gap: 15px;
            }

            .order-status {
                align-self: flex-start;
            }

            .item-card {
                flex-direction: column;
                text-align: center;
            }

            .item-image {
                margin: 0 0 15px 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1 class="page-title">Order Details</h1>
            <a href="orders.php" class="btn btn-secondary">
                <i class="material-icons">arrow_back</i>
                Back to Orders
            </a>
        </div>
    </div>

    <div class="order-container">
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <h2>Order #<?php echo $order['id']; ?></h2>
                    <p class="order-date">Placed on <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                </div>
                <div class="order-status" style="background-color: <?php echo $status_color; ?>20; color: <?php echo $status_color; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </div>
            </div>

            <div class="delivery-info">
                <h3 class="section-title">Delivery Information</h3>
                <div class="info-group">
                    <div class="info-label">Delivery Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['address']); ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value"><?php echo ucfirst($order['payment_method']); ?></div>
                </div>
            </div>

            <div class="items-list">
                <h3 class="section-title">Order Items</h3>
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="item-details">
                            <h4 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p class="item-price"><?php echo formatPrice($item['price']); ?></p>
                            <p class="item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="order-summary">
                <h3 class="section-title">Order Summary</h3>
                <div class="summary-row">
                    <span>Items Total</span>
                    <span><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery Fee</span>
                    <span>₹0.00</span>
                </div>
                <div class="summary-row">
                    <span>Total Amount</span>
                    <span><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 