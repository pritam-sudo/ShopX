<?php
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = getOrderById($order_id);

// Redirect if order not found or not belongs to user
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: index.php');
    exit;
}

// Get order items
$items = getOrderItems($order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - ShopX</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            text-align: center;
        }

        .success-icon {
            font-size: 64px;
            color: #00b894;
            margin-bottom: 20px;
        }

        .success-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .success-message {
            color: #666;
            margin-bottom: 30px;
        }

        .order-details {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-item {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
        }

        .continue-shopping {
            display: inline-block;
            padding: 12px 24px;
            background: #FF1493;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .continue-shopping:hover {
            background: #ff0084;
            transform: translateY(-2px);
        }

        .order-items {
            margin-top: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-name {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .order-item-price {
            color: #FF1493;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1 class="success-title">Order Placed Successfully!</h1>
        <p class="success-message">Thank you for your purchase. Your order has been received and is being processed.</p>

        <div class="order-details">
            <div class="detail-item">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">#<?php echo $order['id']; ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">
                    <?php 
                    echo $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'UPI Payment';
                    ?>
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value"><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Delivery Address:</span>
                <span class="detail-value"><?php echo nl2br(htmlspecialchars($order['address'])); ?></span>
            </div>
        </div>

        <div class="order-items">
            <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         class="order-item-image">
                    <div class="order-item-details">
                        <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="order-item-price"><?php echo formatPrice($item['price']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="index.php" class="continue-shopping">
            Continue Shopping
        </a>
    </div>
</body>
</html> 