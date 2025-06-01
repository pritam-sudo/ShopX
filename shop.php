<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop - ShopX</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    .filters {
    padding: 10px;
    background: rgba(255, 255, 255, 0);
    border-radius: 8px;
    margin-bottom: 0px;
    /* box-shadow: 0 2px 4px rgba(0,0,0,0.1); */
    }

    .filter-section {
      margin-bottom: 15px;
    }

    .filter-section h3 {
      font-size: 16px;
      margin-bottom: 10px;
      color: #333;
    }

    .category-filters {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .category-filter {
      padding: 6px 12px;
      background: #dad7d7;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.3s;
      font-size: 12px;
    }

    .category-filter.active {
      background: #FF1493;
      color: white;
    }

    @media (min-width: 768px) {
      .category-filter {
        padding: 8px 15px;
        font-size: 14px;
      }
    }

    .sort-section {
      padding: 0 10px;
      display: flex;
      justify-content: flex-end;
      margin-bottom: 20px;
    }

    .sort-select {
      width: 100%;
      max-width: 200px;
      font-size: 12px;
    }

    @media (min-width: 768px) {
      .sort-select {
        font-size: 14px;
      }
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
      padding: 10px;
    }

    @media (min-width: 768px) {
      .product-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        padding: 15px;
      }
    }

    @media (min-width: 1024px) {
      .product-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    .product-card {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-image {
      width: 100%;
      height: 120px;
      overflow: hidden;
    }

    @media (min-width: 768px) {
      .product-image {
        height: 150px;
      }
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-info {
      padding: 8px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .product-info h3 {
      margin: 0 0 5px 0;
      font-size: 13px;
      color: #333;
      line-height: 1.2;
    }

    @media (min-width: 768px) {
      .product-info h3 {
        font-size: 14px;
      }
    }

    .product-specs {
      font-size: 11px;
      color: #666;
      margin-bottom: 5px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      line-height: 1.3;
    }

    @media (min-width: 768px) {
      .product-specs {
        font-size: 12px;
      }
    }

    .product-price {
      font-size: 14px;
      font-weight: bold;
      color: #FF1493;
      margin-bottom: 5px;
    }

    @media (min-width: 768px) {
      .product-price {
        font-size: 16px;
      }
    }

    .view-btn {
      width: 100%;
      padding: 6px;
      background-color: #FF1493;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 11px;
      cursor: pointer;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      text-decoration: none;
      margin-top: auto;
    }

    @media (min-width: 768px) {
      .view-btn {
        padding: 8px;
        font-size: 12px;
        gap: 5px;
      }
    }

    .view-btn:hover {
      background-color: #ff0084;
      transform: translateY(-2px);
    }

    .view-btn i {
      font-size: 14px;
    }

    @media (min-width: 768px) {
      .view-btn i {
        font-size: 16px;
      }
    }

    .no-products {
      text-align: center;
      padding: 40px;
      color: #666;
    }
    .search-container {
    padding: 15px;
    background: repeating-linear-gradient(rgb(255, 20, 147), rgba(245, 245, 245, 0.0));
    position: relative;
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
    <input type="text" placeholder="Search for products..." class="search-input" id="searchInput">
    <button class="search-button"><i class="material-icons">search</i></button>
  </div>

  <!-- Filters -->
  <div class="filters">
    <div class="filter-section">
      
      <div class="category-filters">
        <div class="category-filter active" data-category="all">All</div>
        <div class="category-filter" data-category="mobiles">Mobiles</div>
        <div class="category-filter" data-category="laptops">Laptops</div>
        <div class="category-filter" data-category="watches">Watches</div>
        <div class="category-filter" data-category="audio">Audio</div>
      </div>
    </div>
  </div>

  

  <!-- Products Grid -->
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
      foreach ($products as $product) {
        ?>
        <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>" 
             data-price="<?php echo $product['price']; ?>">
          <div class="product-image">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                 loading="lazy"
                 onerror="this.src='uploads/default-product.jpg'">
          </div>
          <div class="product-info">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p class="product-specs"><?php echo htmlspecialchars($product['description']); ?></p>
            <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
            <a href="product.php?id=<?php echo (int)$product['id']; ?>" class="view-btn">
              <i class="material-icons">visibility</i>
              View Details
            </a>
          </div>
        </div>
        <?php
      }
    }
    ?>
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
    <a href="shop.php" class="active">
      <i class="material-icons">shopping_cart</i>
      <span>Shop</span>
    </a>
    <a href="profile.php">
      <i class="material-icons">person</i>
      <span>Profile</span>
    </a>
  </nav>

  <script>
    // Filter and Sort Functionality
    document.addEventListener('DOMContentLoaded', function() {
      const products = document.querySelectorAll('.product-card');
      const categoryFilters = document.querySelectorAll('.category-filter');
      const sortSelect = document.getElementById('sortSelect');
      const searchInput = document.getElementById('searchInput');

      // Category Filter
      categoryFilters.forEach(filter => {
        filter.addEventListener('click', () => {
          categoryFilters.forEach(f => f.classList.remove('active'));
          filter.classList.add('active');
          applyFilters();
        });
      });

      // Sort Change
      sortSelect.addEventListener('change', applyFilters);

      // Search Input
      searchInput.addEventListener('input', applyFilters);

      function applyFilters() {
        const selectedCategory = document.querySelector('.category-filter.active').dataset.category;
        const sortValue = sortSelect.value;
        const searchValue = searchInput.value.toLowerCase();

        products.forEach(product => {
          const productCategory = product.dataset.category;
          const productName = product.querySelector('h3').textContent.toLowerCase();
          const productDesc = product.querySelector('.product-specs').textContent.toLowerCase();

          const matchesCategory = selectedCategory === 'all' || productCategory === selectedCategory;
          const matchesSearch = productName.includes(searchValue) || productDesc.includes(searchValue);

          product.style.display = matchesCategory && matchesSearch ? 'block' : 'none';
        });

        // Sort products
        const productArray = Array.from(products);
        productArray.sort((a, b) => {
          const priceA = parseFloat(a.dataset.price);
          const priceB = parseFloat(b.dataset.price);

          switch(sortValue) {
            case 'price_low':
              return priceA - priceB;
            case 'price_high':
              return priceB - priceA;
            case 'newest':
              return b.dataset.date - a.dataset.date;
            default:
              return 0;
          }
        });

        const productGrid = document.querySelector('.product-grid');
        productArray.forEach(product => productGrid.appendChild(product));
      }
    });
  </script>
</body>
</html> 