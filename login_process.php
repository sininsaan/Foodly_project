<?php
session_start();
include('includes/db.php');

$roll_no = mysqli_real_escape_string($conn, trim($_POST['roll_no'] ?? ''));
$password = $_POST['password'] ?? '';

if(empty($roll_no) || empty($password)) {
    header("Location: index.php?error=invalid");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM users WHERE roll_no='$roll_no'");

if($result && $row = mysqli_fetch_assoc($result)) {
    // Support both hashed and plain text passwords
    $verified = false;
    if(password_verify($password, $row['password'])) {
        $verified = true;
    } elseif($password === $row['password']) {
        $verified = true;
    }

    if($verified) {
        $_SESSION['roll_no'] = $row['roll_no'];
        $_SESSION['name']    = $row['name'] ?: $row['roll_no'];
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email']   = $row['email'];
        $_SESSION['phone']   = $row['phone'];
        header("Location: dashboard.php");
        exit();
    }
}

header("Location: index.php?error=invalid");
exit();
