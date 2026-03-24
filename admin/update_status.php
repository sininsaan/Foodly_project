<?php
session_start();
include('../includes/db.php');
header('Content-Type: application/json');

$order_id = (int)($_POST['order_id'] ?? 0);
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

$allowed = ['Pending','Preparing','Ready','Completed','Missed'];
if(!in_array($status, $allowed) || $order_id <= 0) {
    echo json_encode(['success' => false]);
    exit;
}

$result = mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
echo json_encode(['success' => (bool)$result]);
