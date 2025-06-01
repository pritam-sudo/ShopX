<!-- upload.php (Handles Upload) -->
<?php
include 'functions.php';

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $image_url = '';

    // Debug information
    error_log("Received POST data: " . print_r($_POST, true));
    error_log("Received FILES data: " . print_r($_FILES, true));

    // Validate required fields
    if (empty($name) || empty($description) || empty($price) || empty($category)) {
        error_log("Validation failed: Required fields missing");
        header('Location: add-new-product.php?message=' . urlencode('All fields are required'));
        exit;
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            error_log("Invalid file type: " . $file_type);
            header('Location: add-new-product.php?message=' . urlencode('Only JPG, PNG, GIF, and WEBP images are allowed'));
            exit;
        }

        // Generate unique filename
        $info = pathinfo($_FILES['image']['name']);
        $ext = strtolower($info['extension']);
        $unique_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
        $image_url = 'uploads/' . $unique_name;

        // Move uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_url)) {
            error_log("Failed to move uploaded file to: " . $image_url);
            header('Location: add-new-product.php?message=' . urlencode('Failed to upload image'));
            exit;
        }

        // Verify the file was actually uploaded
        if (!file_exists($image_url)) {
            error_log("File not found after upload: " . $image_url);
            header('Location: add-new-product.php?message=' . urlencode('Failed to save image file'));
            exit;
        }
    } else {
        error_log("No image uploaded or upload error: " . ($_FILES['image']['error'] ?? 'No file'));
        header('Location: add-new-product.php?message=' . urlencode('Image is required'));
        exit;
    }

    // Add product to database
    error_log("Attempting to add product with data: " . print_r([
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'category' => $category,
        'image_url' => $image_url
    ], true));

    $product_id = addProduct($name, $description, $price, $image_url, $category);
    
    if ($product_id) {
        error_log("Product added successfully with ID: " . $product_id);
        header('Location: add-new-product.php?message=' . urlencode('Product added successfully!'));
    } else {
        error_log("Failed to add product to database");
        // If product addition fails, delete the uploaded image
        if (file_exists($image_url)) {
            unlink($image_url);
        }
        header('Location: add-new-product.php?message=' . urlencode('Failed to add product to database'));
    }
    exit;
}

// If not POST request, redirect to admin page
header('Location: add-new-product.php');
exit;
?>

