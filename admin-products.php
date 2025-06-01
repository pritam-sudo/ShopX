<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

require_once 'functions.php';

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteProduct($_GET['delete'])) {
        header('Location: admin-products.php?success=Product deleted successfully');
        exit();
    } else {
        header('Location: admin-products.php?error=Failed to delete product');
        exit();
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$products = array();

if (!empty($search)) {
    // Search products
    $query = "SELECT * FROM products WHERE 
              name LIKE ? OR 
              description LIKE ? OR 
              category LIKE ? 
              ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $query);
    $search_param = "%$search%";
    mysqli_stmt_bind_param($stmt, "sss", $search_param, $search_param, $search_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
} else {
    // Get all products
    $products = getProducts();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - ShopX Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 1rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,.1);
        }
        .main-content {
            padding: 20px;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .search-container {
            max-width: 400px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4>ShopX Admin</h4>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin-dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin-products.php">
                                <i class="bi bi-box me-2"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-orders.php">
                                <i class="bi bi-cart me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-users.php">
                                <i class="bi bi-people me-2"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin-logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Products Management</h2>
                    <a href="add-product.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add New Product
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Search Bar -->
                <div class="search-container">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" 
                               placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="admin-products.php" class="btn btn-outline-secondary ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No products found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     class="product-image">
                                            </td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                            <td>
                                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="admin-products.php?delete=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 