<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized');
}

require_once 'functions.php';

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    http_response_code(400);
    exit('Invalid user ID');
}

$user_id = (int)$_GET['user_id'];
$orders = getUserOrders($user_id);

if (empty($orders)) {
    echo '<div class="alert alert-info">No orders found for this user.</div>';
    exit();
}
?>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
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
                <td><?php echo ucfirst($order['payment_method']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div> 