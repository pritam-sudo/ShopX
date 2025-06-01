<!-- index.html (Shopping Homepage) -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopX - Your Online Store</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    .view-btn {
      width: 100%;
      padding: 10px;
      background-color: #FF1493;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
    }

    .view-btn:hover {
      background-color: #ff0084;
      transform: translateY(-2px);
    }

    .view-btn svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
    }
    .search-container {
    padding: 15px;
    background: repeating-linear-gradient(rgb(255, 20, 147), rgba(245, 245, 245, 0.0));
    position: relative;
    }
    
    .categories {
    padding: 20px 0px 0px 0px;
    }

    .featured-products {
    padding: 9px 14px;
    background-color: rgb(255, 224, 232);
    border-radius: 20px;
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

  <!-- Search Bar -->
  <div class="search-container">
    <input type="text" placeholder="Search for products..." class="search-input">
    <button class="search-button"><i class="material-icons">search</i></button>
  </div>

  <!-- Banner Slider -->
  <div class="banner-slider" style="border-radius: 20px;  margin: 0px 16px;">
    <div class="banner-images">
      <img src="uploads/680be4bc0a4ea.jpeg" alt="Special Offer" style="width: 100%; height: 25vh; object-fit: cover;">
      <img src="uploads/680be4bc0a4ea.jpeg" alt="New Arrivals" style="width: 100%; height: 25vh; object-fit: cover;">
      <img src="uploads/680be4bc0a4ea.jpeg" alt="Seasonal Sale" style="width: 100%; height: 25vh; object-fit: cover;">
    </div>
    <div class="banner-dots">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>
  </div>

  <style>
    .banner-slider {
      position: relative;
      overflow: hidden;
      background: #f5f5f5;
    }

    .banner-images {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }

    .banner-images img {
      flex-shrink: 0;
    }

    .banner-dots {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
    }

    .dot {
      width: 12px;
      height: 12px;
      background: rgba(255, 255, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
    }

    .dot.active {
      background: white;
    }
  </style>

  <script>
    const bannerImages = document.querySelector('.banner-images');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;

    function showSlide(index) {
      bannerImages.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach(dot => dot.classList.remove('active'));
      dots[index].classList.add('active');
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % 3;
      showSlide(currentSlide);
    }

    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        currentSlide = index;
        showSlide(currentSlide);
      });
    });

    setInterval(nextSlide, 5000);
  </script>

  <!-- Categories -->
  <section class="categories">
    <div class="category-grid">
      <a href="#mobiles" class="category-item">
        <div class="category-icon" style="background-color: #FF1493;">
          <i class="material-icons">smartphone</i>
        </div>
        <span>Mobiles</span>
      </a>
      <a href="#laptops" class="category-item">
        <div class="category-icon" style="background-color: #FFD700;">
          <i class="material-icons">laptop</i>
        </div>
        <span>Laptops</span>
      </a>
      <a href="#watches" class="category-item">
        <div class="category-icon" style="background-color: #00A67E;">
          <i class="material-icons">watch</i>
        </div>
        <span>Watches</span>
      </a>
      <a href="#audio" class="category-item">
        <div class="category-icon" style="background-color: #FF4444;">
          <i class="material-icons">headphones</i>
        </div>
        <span>Audio</span>
      </a>
    </div>
  </section>

  <!-- Featured Products -->
  <section class="featured-products">
  <h2 style="text-align: center; font-size: 25px; color:rgb(26, 23, 24); margin: 0px 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Our Best Products</h2>
    <h2 style="text-align: center; font-size: 20px; color: #FF1493; margin: 0px 0px 10px 0px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);"> For You</h2>
    
    <div class="product-grid">
      <?php
      include 'functions.php';
      $products = getProducts();
      
      if (empty($products)) {
        echo '<div class="no-products">';
        echo '<p>No products available at the moment.</p>';
        echo '<p>Please check back later or contact the administrator.</p>';
        echo '</div>';
      } else {
        // Shuffle the products array randomly
        shuffle($products);
        
        // Take only first 20 products
        $products = array_slice($products, 0, 20);

        foreach ($products as $product) {
          ?>
          <div class="product-card">
            <div class="product-image">
              <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                   alt="<?php echo htmlspecialchars($product['name']); ?>"
                   loading="lazy"
                   onerror="this.src='uploads/default-product.jpg'">
            </div>
            <div class="product-info">
              <h3><?php echo htmlspecialchars($product['name']); ?></h3>
              <!-- <p class="product-specs"><?php echo htmlspecialchars($product['description']); ?></p> -->
              <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
              <a href="product.php?id=<?php echo (int)$product['id']; ?>" class="view-btn">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                </svg>
                View
              </a>
            </div>
          </div>
          <?php
        }
      }
      ?>
    </div>
  </section>
  
  <!-- Slider Products -->
  <section class="featured-products" style="margin-top: 20px;">
    <h2 style="text-align: center; font-size: 25px; color:rgb(26, 23, 24); margin: 0px 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">New Arrivals</h2>
    <h2 style="text-align: center; font-size: 20px; color: #FF1493; margin: 0px 0px 10px 0px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Just For You</h2>
    
    <div class="product-slider">
      <div class="slider-container">
        <?php
        // Get products and shuffle randomly
        $products = getProducts();
        shuffle($products);
        
        // Take only first 8 products
        $products = array_slice($products, 0,3);

        foreach ($products as $product) {
          ?>
          <div class="slider-item">
            <div class="product-card">
              <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     loading="lazy"
                     onerror="this.src='uploads/default-product.jpg'">
              </div>
              <div class="product-info">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                <a href="product.php?id=<?php echo (int)$product['id']; ?>" class="view-btn">
                  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                  </svg>
                  View
                </a>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
  </section>

  <style>
    .product-slider {
      width: 100%;
      overflow: hidden;
      position: relative;
    }

    .slider-container {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      -webkit-overflow-scrolling: touch;
      scroll-behavior: smooth;
      gap: 15px;
      padding: 10px 5px;
    }

    .slider-container::-webkit-scrollbar {
      display: none;
    }

    .slider-item {
      flex: 0 0 auto;
      width: calc(50% - 8px);
      scroll-snap-align: start;
    }

    @media (min-width: 768px) {
      .slider-item {
        width: calc(33.333% - 10px);
      }
    }

    @media (min-width: 1024px) {
      .slider-item {
        width: calc(25% - 12px);
      }
    }
  </style>

  <!-- Bottom Navigation -->
  <nav class="bottom-nav">
    <a href="index.php" class="active">
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
    <a href="profile.php">
      <i class="material-icons">person</i>
      <span>Profile</span>
    </a>
  </nav>

  <script>
  function addToCart(productId) {
    // You can implement the add to cart functionality here
    alert('Product added to cart!');
  }
  </script>
</body>
</html>