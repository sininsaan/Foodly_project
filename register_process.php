<?php
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = $_POST['roll_no'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $pass = $_POST['password']; 

    //insert
    $sql = "INSERT INTO users (roll_no, phone, email, password) VALUES ('$roll', '$phone', '$email', '$pass')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Account Created Successfully! You can now login.');
                window.location.href='index.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>