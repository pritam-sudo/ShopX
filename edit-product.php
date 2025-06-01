<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

require_once 'functions.php';

$error = '';
$success = '';

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin-products.php?error=Invalid product ID');
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details
$product = getProductById($product_id);
if (!$product) {
    header('Location: admin-products.php?error=Product not found');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    
    // Validate input
    if (empty($name) || empty($description) || empty($price) || empty($category)) {
        $error = 'All fields are required';
    } else {
        // Handle image upload
        $image_url = $product['image_url']; // Keep existing image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    // Delete old image if it's not the default one
                    if ($image_url !== 'uploads/default-product.jpg' && file_exists($image_url)) {
                        unlink($image_url);
                    }
                    $image_url = $target_path;
                } else {
                    $error = 'Failed to upload image';
                }
            } else {
                $error = 'Invalid file type. Allowed types: JPG, JPEG, PNG, GIF';
            }
        }
        
        if (empty($error)) {
            // Update product in database
            $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssdssi", $name, $description, $price, $image_url, $category, $product_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Product updated successfully';
                // Refresh product data
                $product = getProductById($product_id);
            } else {
                $error = 'Failed to update product';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - ShopX Admin</title>
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
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            margin-top: 10px;
        }
        .current-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            margin-top: 10px;
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
                            <a class="nav-link" href="admin-products.php">
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
                    <h2>Edit Product</h2>
                    <a href="admin-products.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Products
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price (₹)</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Electronics" <?php echo $product['category'] === 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                                            <option value="Clothing" <?php echo $product['category'] === 'Clothing' ? 'selected' : ''; ?>>Clothing</option>
                                            <option value="Books" <?php echo $product['category'] === 'Books' ? 'selected' : ''; ?>>Books</option>
                                            <option value="Home" <?php echo $product['category'] === 'Home' ? 'selected' : ''; ?>>Home</option>
                                            <option value="Sports" <?php echo $product['category'] === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                                            <option value="Beauty" <?php echo $product['category'] === 'Beauty' ? 'selected' : ''; ?>>Beauty</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Product Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <div class="mt-2">
                                            <p class="mb-1">Current Image:</p>
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                 alt="Current Product Image" 
                                                 class="current-image">
                                        </div>
                                        <img id="imagePreview" class="preview-image" src="#" alt="Preview">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>Update Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html> 