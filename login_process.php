<?php
session_start();
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = $_POST['roll_no'];
    $pass = $_POST['password'];

   
    $query = "SELECT * FROM users WHERE roll_no = '$roll' AND password = '$pass'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = $roll; // Session start
        header("Location: dashboard.php"); 
    } else {
        echo "<script>alert('Invalid Roll No or Password!'); window.location.href='index.php';</script>";
    }
}
?>