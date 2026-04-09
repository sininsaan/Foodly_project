<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$id = (int)($_GET['id'] ?? 0);
if($id > 0) {
    if(mysqli_query($conn, "DELETE FROM menu WHERE item_id=$id")) {
        
        $_SESSION['delete_success'] = true;
    }
}
header("Location: menu.php");
exit();
?>
