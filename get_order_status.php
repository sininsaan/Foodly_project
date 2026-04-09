<?php
session_start();
include('includes/db.php');
header('Content-Type: application/json');
$roll_no = mysqli_real_escape_string($conn, $_SESSION['roll_no'] ?? '');
if(!$roll_no) { echo json_encode(['orders'=>[]]); exit; }
$q = mysqli_query($conn, "SELECT id, token, status FROM orders WHERE roll_no='$roll_no' AND DATE(created_at)=CURDATE() AND status NOT IN ('Completed') ORDER BY created_at DESC LIMIT 5");
$orders = [];
while($r = mysqli_fetch_assoc($q)) $orders[] = $r;
echo json_encode(['orders' => $orders]);