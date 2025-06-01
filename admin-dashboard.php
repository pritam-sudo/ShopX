<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

// Include database connection
require_once 'functions.php';

// Get statistics
$total_orders = getTotalOrders();
$total_products = getTotalProducts();
$total_customers = getTotalCustomers();
$total_revenue = getTotalRevenue();
$recent_orders = getRecentOrders(10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ShopX</title>
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
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
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
                            <a class="nav-link active" href="admin-dashboard.php">
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
                    <h2>Dashboard Overview</h2>
                    <div class="text-muted">
                        Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <h2 class="card-text"><?php echo getTotalProducts(); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <h2 class="card-text"><?php echo getTotalOrders(); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2 class="card-text"><?php echo getTotalCustomers(); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Revenue</h5>
                                <h2 class="card-text">₹<?php echo number_format(getTotalRevenue(), 2); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card dashboard-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recent_orders = getRecentOrders(5);
                                    foreach ($recent_orders as $order):
                                        $user = getUserById($order['user_id']);
                                    ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $order['status'] === 'completed' ? 'success' : 
                                                    ($order['status'] === 'processing' ? 'warning' : 
                                                    ($order['status'] === 'cancelled' ? 'danger' : 'info')); 
                                            ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
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