<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

require_once 'functions.php';

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    $force_delete = isset($_GET['force']) && $_GET['force'] === '1';
    
    // Check if user has any orders
    $user_orders = getUserOrders($user_id);
    
    if (!empty($user_orders) && !$force_delete) {
        // Show confirmation modal for users with orders
        $show_force_delete_modal = true;
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            if (!empty($user_orders)) {
                // Delete order items first
                $stmt = mysqli_prepare($conn, "DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)");
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                
                // Delete orders
                $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
            }
            
            // Delete user
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_commit($conn);
                header('Location: admin-users.php?success=User deleted successfully');
                exit();
            } else {
                throw new Exception("Failed to delete user");
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            header('Location: admin-users.php?error=Failed to delete user: ' . $e->getMessage());
            exit();
        }
    }
}

// Get all users
$query = "SELECT id, name, email, mobile, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$users = array();
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - ShopX Admin</title>
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
                            <a class="nav-link active" href="admin-users.php">
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
                    <h2>Users Management</h2>
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

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Joined Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>#<?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewUserOrders(<?php echo $user['id']; ?>)">
                                                <i class="bi bi-cart"></i> Orders
                                            </button>
                                            <a href="admin-users.php?delete=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
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

    <!-- User Orders Modal -->
    <div class="modal fade" id="userOrdersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="userOrdersContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Force Delete Modal -->
    <div class="modal fade" id="forceDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Force Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>This user has existing orders. Deleting the user will also delete all their orders and order items.</p>
                    <p>Are you sure you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="forceDeleteBtn" class="btn btn-danger">Delete User and Orders</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewUserOrders(userId) {
            // Fetch user orders using AJAX
            fetch(`get-user-orders.php?user_id=${userId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('userOrdersContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('userOrdersModal')).show();
                });
        }

        <?php if (isset($show_force_delete_modal) && $show_force_delete_modal): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const forceDeleteModal = new bootstrap.Modal(document.getElementById('forceDeleteModal'));
            const forceDeleteBtn = document.getElementById('forceDeleteBtn');
            
            forceDeleteBtn.href = `admin-users.php?delete=<?php echo $user_id; ?>&force=1`;
            forceDeleteModal.show();
        });
        <?php endif; ?>
    </script>
</body>
</html> 