<?php
session_start();
include('../includes/db.php');

$username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$result = mysqli_query($conn, "SELECT * FROM admins WHERE username='$username'");
if($result && $row = mysqli_fetch_assoc($result)) {
    if(password_verify($password, $row['password'])) {
        $_SESSION['admin'] = $row['id'];
        $_SESSION['admin_username'] = $row['username'];
        header("Location: dashboard.php");
        exit();
    }
}
header("Location: index.php?error=invalid");
exit();
