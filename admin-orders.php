<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

require_once 'functions.php';

// Define valid order statuses
$valid_statuses = ['pending', 'processing', 'shipping', 'out_for_delivery', 'completed', 'cancelled'];

// Handle order status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    // Validate status
    if (!in_array($status, $valid_statuses)) {
        header('Location: admin-orders.php?error=Invalid order status');
        exit();
    }
    
    // Update order status
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: admin-orders.php?success=Order status updated successfully');
        exit();
    } else {
        header('Location: admin-orders.php?error=Failed to update order status');
        exit();
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$orders = array();

if (!empty($search)) {
    // Search orders
    $query = "SELECT o.*, u.name as customer_name 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              WHERE o.id LIKE ? OR 
                    u.name LIKE ? OR 
                    o.status LIKE ? OR 
                    o.payment_method LIKE ?
              ORDER BY o.created_at DESC 
              LIMIT 100";
    $stmt = mysqli_prepare($conn, $query);
    $search_param = "%$search%";
    mysqli_stmt_bind_param($stmt, "ssss", $search_param, $search_param, $search_param, $search_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
} else {
    // Get all orders
    $orders = getRecentOrders(100);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - ShopX Admin</title>
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
        .order-details {
            display: none;
        }
        .order-details.show {
            display: table-row;
        }
        .search-container {
            max-width: 400px;
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; color: #fff; }
        .status-shipping { background-color: #007bff; color: #fff; }
        .status-out_for_delivery { background-color: #6f42c1; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
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
                            <a class="nav-link active" href="admin-orders.php">
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
                    <h2>Orders Management</h2>
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
                               placeholder="Search orders..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="admin-orders.php" class="btn btn-outline-secondary ms-2">
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
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($orders)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No orders found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="shipping" <?php echo $order['status'] === 'shipping' ? 'selected' : ''; ?>>Shipping</option>
                                                        <option value="out_for_delivery" <?php echo $order['status'] === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php 
                                                    $status_display = [
                                                        'pending' => 'Pending',
                                                        'processing' => 'Processing',
                                                        'shipping' => 'Shipping',
                                                        'out_for_delivery' => 'Out for Delivery',
                                                        'completed' => 'Completed',
                                                        'cancelled' => 'Cancelled'
                                                    ];
                                                    echo $status_display[$order['status']] ?? ucfirst($order['status']);
                                                    ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="toggleOrderDetails(<?php echo $order['id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="order-details" id="order-details-<?php echo $order['id']; ?>">
                                            <td colspan="6">
                                                <div class="p-3">
                                                    <h5>Order Details</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <?php 
                                                            $user = getUserById($order['user_id']);
                                                            if ($user): 
                                                            ?>
                                                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                                            <p><strong>Mobile:</strong> <?php echo htmlspecialchars($user['mobile']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Shipping Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                                                            <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                                                        </div>
                                                    </div>
                                                    <h6 class="mt-3">Order Items</h6>
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Quantity</th>
                                                                <th>Price</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $order_items = getOrderItems($order['id']);
                                                            foreach ($order_items as $item): 
                                                                $product = getProductById($item['product_id']);
                                                            ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                                <td><?php echo $item['quantity']; ?></td>
                                                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                                                <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
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
    <script>
        function toggleOrderDetails(orderId) {
            const detailsRow = document.getElementById(`order-details-${orderId}`);
            detailsRow.classList.toggle('show');
        }
    </script>
</body>
</html> 