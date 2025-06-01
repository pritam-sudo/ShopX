<?php
include 'functions.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = getProductById($product_id);

// Redirect if product not found
if (!$product) {
    header('Location: index.php?message=' . urlencode('Product not found'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - ShopX</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .product-detail {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            position: relative;
        }

        .product-image-large {
            width: 100%;
            height: 500px;
            overflow: hidden;
            background: #f8f9fa;
            border-radius: 10px 0 0 10px;
        }

        .product-image-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .product-image-large:hover img {
            transform: scale(1.05);
        }

        .product-info-detailed {
            padding: 30px;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        .product-name {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .product-price-large {
            font-size: 32px;
            color: #FF1493;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-price-large::before {
            content: "₹";
            font-size: 24px;
            color: #666;
        }

        .product-description {
            color: #666;
            line-height: 1.8;
            margin-bottom: 30px;
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 10px;
        }

        .back-to-shop {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s;
            padding: 10px 20px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
        }

        .back-to-shop:hover {
            color: #FF1493;
            background: #fff;
            border-color: #FF1493;
            transform: translateX(-5px);
            box-shadow: 0 2px 8px rgba(255, 20, 147, 0.2);
        }

        .buy-now-btn {
            width: 100%;
            padding: 16px;
            background-color: #FF1493;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: sticky;
            bottom: 0;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(255, 20, 147, 0.3);
        }

        .buy-now-btn:hover {
            background-color: #ff0084;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 20, 147, 0.4);
        }

        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
                margin: 0;
                border-radius: 0;
            }

            .product-image-large {
                height: 300px;
                border-radius: 0;
            }

            .product-info-detailed {
                padding: 20px;
            }

            .back-to-shop {
                top: 10px;
                left: 10px;
                padding: 8px 16px;
            }

            .buy-now-btn {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                margin: 0;
                border-radius: 0;
                z-index: 100;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            }

            .product-detail {
                padding-bottom: 70px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">ShopX</div>
        <div class="header-icons">
            <a href="cart.php" class="cart-icon"><i class="material-icons">shopping_cart</i></a>
            <a href="profile.php" class="profile-icon"><i class="material-icons">person</i></a>
        </div>
    </header>

    <div class="container">
        <a href="index.php" class="back-to-shop">
            <i class="material-icons">arrow_back</i>
            Back to Shop
        </a>

        <div class="product-detail">
            <div class="product-image-large">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='uploads/default-product.jpg'">
            </div>
            <div class="product-info-detailed">
                <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-price-large"><?php echo formatPrice($product['price']); ?></div>
                <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                <div class="product-actions">
                    <a href="checkout.php?product_id=<?php echo $product['id']; ?>" class="buy-now-btn">
                        <i class="material-icons">shopping_cart</i>
                        Buy Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function addToCart(productId) {
        // You can implement the add to cart functionality here
        alert('Product ' + productId + ' added to cart!');
    }
    </script>
</body>
</html> 