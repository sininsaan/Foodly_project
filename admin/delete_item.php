<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$id = (int)($_GET['id'] ?? 0);
if($id > 0) {
    mysqli_query($conn, "DELETE FROM menu WHERE item_id=$id");
}
header("Location: dashboard.php");
exit();
