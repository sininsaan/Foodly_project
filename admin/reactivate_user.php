<?php
session_start();
include('../includes/db.php');

// Admin login check 
if(!isset($_SESSION['admin'])) { 
    header("Location: index.php"); 
    exit(); 
}

if(isset($_GET['roll_no'])) {
    $roll_no = mysqli_real_escape_string($conn, $_GET['roll_no']);
    
    $update_user = mysqli_query($conn, "UPDATE users SET status='active' WHERE roll_no='$roll_no'");
    
    $clear_orders = mysqli_query($conn, "UPDATE orders SET status='Completed' WHERE roll_no='$roll_no' AND status='Missed'");

    if($update_user && $clear_orders) {
        header("Location: users.php?msg=activated");
    } else {
        header("Location: users.php?error=failed");
    }
    exit();
} else {
    header("Location: users.php");
    exit();
}
?>