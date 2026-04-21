<?php
include('includes/db.php');

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

// Delete old and reinsert with fresh hash
mysqli_query($conn, "DELETE FROM admins WHERE username='admin'");
$result = mysqli_query($conn, "INSERT INTO admins (username, password) VALUES ('$username', '$hash')");

if($result) {
    echo "<h2 style='font-family:sans-serif;color:green;padding:40px;'>✅ Admin created successfully!<br><br>
    Username: <strong>admin</strong><br>
    Password: <strong>admin123</strong><br><br>
    <a href='admin/index.php'>Go to Admin Login →</a><br><br>
    <strong style='color:red;'>⚠️ Delete this file (setup_admin.php) after logging in!</strong>
    </h2>";
} else {
    echo "<h2 style='font-family:sans-serif;color:red;padding:40px;'>❌ Error: " . mysqli_error($conn) . "</h2>";
}
?>
