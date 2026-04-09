<?php
session_start();
include('../includes/db.php');
header('Content-Type: application/json');
if(!isset($_SESSION['admin'])) { echo json_encode(['orders'=>[]]); exit; }
$q = mysqli_query($conn, "SELECT o.*, GROUP_CONCAT(CONCAT(oi.item_name,' x',oi.quantity) SEPARATOR ', ') as items, ts.label as slot, DATE_FORMAT(o.created_at,'%h:%i %p') as time FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id LEFT JOIN time_slots ts ON o.slot_id=ts.id WHERE DATE(o.created_at)=CURDATE() GROUP BY o.id ORDER BY o.created_at DESC");
$orders = [];
while($r = mysqli_fetch_assoc($q)) $orders[] = $r;
echo json_encode(['orders' => $orders]);