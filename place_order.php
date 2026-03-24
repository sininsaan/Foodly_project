<?php
session_start();
include('includes/db.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if(!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'No items']);
    exit;
}

$roll_no  = isset($_SESSION['roll_no']) ? mysqli_real_escape_string($conn, $_SESSION['roll_no']) : '0000';
$slot_id  = (int)($data['slot_id'] ?? 1);
$total    = (float)($data['total'] ?? 0);
$items    = $data['items'];

// Generate daily token — resets every day starting from 101
$token_q = mysqli_query($conn, "SELECT MAX(token) as max_token FROM orders WHERE DATE(created_at) = CURDATE()");
$token_row = mysqli_fetch_assoc($token_q);
$token = ($token_row['max_token'] && $token_row['max_token'] >= 1) 
         ? ($token_row['max_token'] + 1) 
         : 1;

// Insert order
$insert = mysqli_query($conn, "INSERT INTO orders (roll_no, slot_id, total, token, status, created_at)
                               VALUES ('$roll_no', $slot_id, $total, $token, 'Pending', NOW())");

if(!$insert) {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    exit;
}

$order_id = mysqli_insert_id($conn);

// Insert order items
foreach($items as $item) {
    $name  = mysqli_real_escape_string($conn, $item['name']);
    $price = (float)$item['price'];
    $qty   = (int)$item['qty'];
    mysqli_query($conn, "INSERT INTO order_items (order_id, item_name, price, quantity)
                         VALUES ($order_id, '$name', $price, $qty)");
}

// Update slot count
mysqli_query($conn, "UPDATE time_slots SET current_count = current_count + 1 WHERE id = $slot_id");

echo json_encode(['success' => true, 'token' => $token, 'order_id' => $order_id]);
