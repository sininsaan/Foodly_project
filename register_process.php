<?php
session_start();
include('includes/db.php');

$roll_no   = mysqli_real_escape_string($conn, trim($_POST['roll_no'] ?? ''));
$name      = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
$email     = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
$phone     = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$password  = $_POST['password'] ?? '';

// Validation
if(empty($roll_no) || empty($name) || empty($password)) {
    header("Location: index.php?error=invalid");
    exit();
}

// Check if roll_no already exists
$check = mysqli_query($conn, "SELECT id FROM users WHERE roll_no='$roll_no'");
if($check && mysqli_num_rows($check) > 0) {
    header("Location: index.php?error=exists");
    exit();
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

$result = mysqli_query($conn, "INSERT INTO users (roll_no, name, email, phone, password) 
                               VALUES ('$roll_no', '$name', '$email', '$phone', '$hashed')");

if($result) {
    header("Location: index.php?error=registered");
    exit();
} else {
    header("Location: index.php?error=invalid");
    exit();
}
