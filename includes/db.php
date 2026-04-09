<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "foodly_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- AUTOMATION LOGIC FOR DAILY SLOT RESET ---//
$today = date('Y-m-d');

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS system_settings (last_reset DATE)");

$res = mysqli_query($conn, "SELECT last_reset FROM system_settings LIMIT 1");
$row = mysqli_fetch_assoc($res);

if (!$row || $row['last_reset'] != $today) {
    
    $q = "UPDATE time_slots SET current_count = 0"; 
    
    if(mysqli_query($conn, $q)) {
        mysqli_query($conn, "DELETE FROM system_settings"); 
        mysqli_query($conn, "INSERT INTO system_settings VALUES ('$today')");
        echo "<script>console.log('Automation Success: Slots Reset!');</script>";
    } else {
        echo "<script>console.error('SQL Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>