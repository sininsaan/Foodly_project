<?php
session_start();
include('../includes/db.php');
header('Content-Type: application/json');

$item_id = (int)($_POST['item_id'] ?? 0);
$available = (int)($_POST['available'] ?? 0);

$result = mysqli_query($conn, "UPDATE menu SET is_available=$available WHERE item_id=$item_id");
echo json_encode(['success' => (bool)$result]);
