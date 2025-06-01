<?php
session_start();

// Default admin credentials
$admin_username = 'admin';
$admin_password = 'admin123';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate credentials
    if ($username === $admin_username && $password === $admin_password) {
        // Set admin session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // Redirect to admin dashboard
        header('Location: admin-dashboard.php');
        exit();
    } else {
        // Invalid credentials
        header('Location: admin-login.php?error=Invalid username or password');
        exit();
    }
} else {
    // If not POST request, redirect to login
    header('Location: admin-login.php');
    exit();
} 