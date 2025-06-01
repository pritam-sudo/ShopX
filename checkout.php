<?php
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user details
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

// Get product details from session or URL
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product = $product_id ? getProductById($product_id) : null;

if (!$product) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'cod';
    
    if (empty($address)) {
        $error = 'Please enter your delivery address';
    } else {
        // Create order
        $order_id = createOrder($user_id, $product['price'], $address, $payment_method);
        if ($order_id) {
            // Add order item
            if (addOrderItem($order_id, $product['id'], 1, $product['price'])) {
                // Redirect to order success page
                header("Location: order-success.php?id=$order_id");
                exit;
            } else {
                $error = 'Failed to place order. Please try again.';
            }
        } else {
            $error = 'Failed to create order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ShopX</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            padding-bottom: 80px;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .checkout-title {
            font-size: 24px;
            color: #333;
            font-weight: 600;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .checkout-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .user-info {
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 500;
            color: #666;
        }

        .info-value {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            resize: vertical;
            min-height: 100px;
        }

        .product-details {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .product-price {
            font-size: 16px;
            color: #FF1493;
            font-weight: 500;
        }

        .payment-method {
            margin-bottom: 20px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option:hover {
            border-color: #FF1493;
        }

        .payment-option.selected {
            border-color: #FF1493;
            background: rgba(255, 20, 147, 0.05);
        }

        .payment-icon {
            font-size: 24px;
            color: #FF1493;
        }

        .payment-label {
            font-weight: 500;
        }

        .place-order-btn {
            width: 100%;
            padding: 15px;
            background: #FF1493;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .place-order-btn:hover {
            background: #ff0084;
            transform: translateY(-2px);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .message.error {
            background: #ffe6e6;
            color: #d63031;
            border: 1px solid #fab1a0;
        }

        .message.success {
            background: #e6ffe6;
            color: #00b894;
            border: 1px solid #55efc4;
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            
            .place-order-btn {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                border-radius: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="checkout-header">
            <h1 class="checkout-title">Checkout</h1>
        </div>

        <div class="checkout-grid">
            <div class="checkout-section">
                <h2 class="section-title">Delivery Information</h2>
                
                <div class="user-info">
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Mobile:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user['mobile']); ?></span>
                    </div>
                </div>

                <form action="checkout.php?product_id=<?php echo $product_id; ?>" method="post">
                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <textarea id="address" name="address" required 
                                  placeholder="Enter your complete delivery address"></textarea>
                    </div>

                    <h2 class="section-title">Payment Method</h2>
                    <div class="payment-method">
                        <div class="payment-option">
                            <input type="radio" id="cod" name="payment_method" value="cod" checked>
                            <label for="cod">
                                <span class="payment-icon">💵</span>
                                <span class="payment-label">Cash on Delivery</span>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="upi" name="payment_method" value="upi">
                            <label for="upi">
                                <span class="payment-icon">🏦</span>
                                <span class="payment-label">UPI Payment</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="place-order-btn">
                        Place Order
                    </button>
                </form>
            </div>

            <div class="checkout-section">
                <h2 class="section-title">Order Summary</h2>
                
                <div class="product-details">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="product-image">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price"><?php echo formatPrice($product['price']); ?></p>
                    </div>
                </div>

                <div class="order-summary">
                    <div class="info-item">
                        <span class="info-label">Subtotal:</span>
                        <span class="info-value"><?php echo formatPrice($product['price']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Delivery:</span>
                        <span class="info-value">Free</span>
                    </div>
                    <div class="info-item" style="font-size: 18px; font-weight: 600;">
                        <span class="info-label">Total:</span>
                        <span class="info-value"><?php echo formatPrice($product['price']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 