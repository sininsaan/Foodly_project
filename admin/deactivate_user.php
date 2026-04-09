<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

if(isset($_GET['roll_no'])) {
    $roll_no = mysqli_real_escape_string($conn, $_GET['roll_no']);
   
    $update = mysqli_query($conn, "UPDATE users SET status='suspended' WHERE roll_no='$roll_no'");

    if($update) {
        header("Location: users.php?msg=deactivated");
    } else {
        header("Location: users.php?error=failed");
    }
}
?>