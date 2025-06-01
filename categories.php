<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopX - Categories</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    .categories-page {
      padding: 20px;
      background-color: rgb(255, 224, 232);
      min-height: calc(100vh - 120px);
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      padding: 20px 0;
    }

    @media (min-width: 768px) {
      .categories-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (min-width: 1024px) {
      .categories-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    .category-card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      text-decoration: none;
      color: inherit;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
    }

    .category-card:hover {
      transform: translateY(-5px);
    }

    .category-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
    }

    .category-icon i {
      color: white;
      font-size: 30px;
    }

    .category-name {
      font-size: 18px;
      font-weight: 500;
    }

    .category-count {
      color: #666;
      font-size: 14px;
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

  <main class="categories-page">
    <h2 style="text-align: center; font-size: 25px; color:rgb(26, 23, 24); margin: 0px 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Shop by Category</h2>
    <h2 style="text-align: center; font-size: 20px; color: #FF1493; margin: 0px 0px 10px 0px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">Find What You Love</h2>

    <div class="categories-grid">
    <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); padding: 30px 0;"></span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>

      <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>

      <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>

      <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>
      <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>

      <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>

      <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>

      <a href="category.php?type=laptops" class="category-card" style="background-image: url('uploads/680be4bc0a4ea.jpeg'); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content: flex-end; padding-bottom: 20px;">
        <span class="category-name" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">Laptops</span>
        <span class="category-count" style="color: #fff; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">100+ Products</span>
      </a>
<!-- 
      <a href="category.php?type=gaming" class="category-card">
        <div class="category-icon" style="background-color: #FF9800;">
          <i class="material-icons">sports_esports</i>
        </div>
        <span class="category-name">Gaming</span>
        <span class="category-count">90+ Products</span>
      </a> -->
    </div>
  </main>

  <!-- Bottom Navigation -->
  <nav class="bottom-nav">
    <a href="index.php">
      <i class="material-icons">home</i>
      <span>Home</span>
    </a>
    <a href="categories.php" class="active">
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
</body>
</html>
