<?php
session_start();
include('includes/db.php');

$student_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
$student_roll = isset($_SESSION['roll_no']) ? $_SESSION['roll_no'] : '0000';
$student_initial = strtoupper(substr($student_name, 0, 1));

// Active orders (Pending, Preparing, Ready)
$active_query = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.item_name, ' x', oi.quantity) SEPARATOR ', ') as items_summary,
                 ts.label as slot
                 FROM orders o
                 LEFT JOIN order_items oi ON o.id = oi.order_id
                 LEFT JOIN time_slots ts ON o.slot_id = ts.id
                 WHERE o.roll_no = '$student_roll'
                 AND o.status NOT IN ('Completed', 'Missed')
                 GROUP BY o.id ORDER BY o.created_at DESC";
$active_result = mysqli_query($conn, $active_query);

// Past orders
$history_query = "SELECT o.*, GROUP_CONCAT(CONCAT(oi.item_name, ' x', oi.quantity) SEPARATOR ', ') as items_summary,
                  ts.label as slot
                  FROM orders o
                  LEFT JOIN order_items oi ON o.id = oi.order_id
                  LEFT JOIN time_slots ts ON o.slot_id = ts.id
                  WHERE o.roll_no = '$student_roll'
                  AND o.status IN ('Completed', 'Missed')
                  GROUP BY o.id ORDER BY o.created_at DESC LIMIT 50";
$history_result = mysqli_query($conn, $history_query);

$demo_active = [];

$demo_history = [
    ['id'=>401,'token'=>401,'items_summary'=>'Butter Naan with Chole x1, Masala Tea x1','total'=>110,'slot'=>'12:00 PM - 1:00 PM','status'=>'Completed','created_at'=>date('Y-m-d H:i:s',strtotime('-2 days'))],
    ['id'=>398,'token'=>398,'items_summary'=>'Veg Samosa x3','total'=>45,'slot'=>'11:00 AM - 12:00 PM','status'=>'Missed','created_at'=>date('Y-m-d H:i:s',strtotime('-5 days'))],
    ['id'=>390,'token'=>390,'items_summary'=>'Dal Makhani Rice x1, Masala Tea x2','total'=>90,'slot'=>'1:00 PM - 2:00 PM','status'=>'Completed','created_at'=>date('Y-m-d H:i:s',strtotime('-8 days'))],
];

$active_orders = [];
if($active_result && mysqli_num_rows($active_result) > 0) {
    while($r = mysqli_fetch_assoc($active_result)) $active_orders[] = $r;
} else {
    $active_orders = $demo_active;
}

$display_orders = [];
if($history_result && mysqli_num_rows($history_result) > 0) {
    while($r = mysqli_fetch_assoc($history_result)) $display_orders[] = $r;
} else {
    $display_orders = $demo_history;
}

// Stats
$total_orders = count($display_orders);
$completed = 0; $total_spent = 0;
foreach($display_orders as $o) {
    if($o['status'] === 'Completed') { $completed++; $total_spent += $o['total']; }
}
$pickup_rate = $total_orders > 0 ? round(($completed/$total_orders)*100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Order History</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Smaller stat cards */
        .stat-card-sm {
            background: var(--bg-card);
            border-radius: var(--radius-sm);
            padding: 14px 18px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            pointer-events: none;
        }
        .stat-icon-sm {
            font-size: 18px;
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon-sm.orange { background: rgba(232,67,26,0.1); }
        .stat-icon-sm.green  { background: rgba(45,154,95,0.1); }
        .stat-icon-sm.blue   { background: rgba(37,99,235,0.1); }
        .stat-icon-sm.purple { background: rgba(124,58,237,0.1); }
        .stat-val-sm {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }
        .stat-lbl-sm {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Active order card */
        .active-order-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1.5px solid var(--primary);
            padding: 18px 22px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .active-order-token {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
        }
        .active-order-label {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 600;
        }
        .active-status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🍽️</div>
        <a href="index.php" style="text-decoration:none;"><span class="logo-text">Foodly</span></a>
    </div>
    <p class="nav-label">Main Menu</p>
    <a class="nav-item" href="dashboard.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        Menu
    </a>
    <a class="nav-item active" href="history.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        My Orders
    </a>
    <div class="sidebar-footer">
        <a href="profile.php" style="text-decoration:none;">
            <div class="user-pill" style="cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                <div class="user-avatar"><?php echo $student_initial; ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($student_name); ?></div>
                    <div class="user-roll">Roll No: <?php echo htmlspecialchars($student_roll); ?></div>
                </div>
            </div>
        </a>
        <a class="nav-item" href="logout.php" style="margin-top:8px;">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Logout
        </a>
    </div>
</aside>

<main class="main-content" style="padding-bottom:100px;">
    <div class="page-header">
        <div>
            <h1 class="page-title">My Orders</h1>
            <p class="page-subtitle">Active orders & past history</p>
        </div>
    </div>

    <!-- Small Stat Cards -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px;">
        <div class="stat-card-sm">
            <div class="stat-icon-sm orange">📋</div>
            <div>
                <div class="stat-val-sm"><?php echo $total_orders; ?></div>
                <div class="stat-lbl-sm">Total Orders</div>
            </div>
        </div>
        <div class="stat-card-sm">
            <div class="stat-icon-sm green">✅</div>
            <div>
                <div class="stat-val-sm"><?php echo $completed; ?></div>
                <div class="stat-lbl-sm">Completed</div>
            </div>
        </div>
        <div class="stat-card-sm">
            <div class="stat-icon-sm blue">💰</div>
            <div>
                <div class="stat-val-sm">₹<?php echo number_format($total_spent); ?></div>
                <div class="stat-lbl-sm">Total Spent</div>
            </div>
        </div>
        <div class="stat-card-sm">
            <div class="stat-icon-sm purple">🎯</div>
            <div>
                <div class="stat-val-sm"><?php echo $pickup_rate; ?>%</div>
                <div class="stat-lbl-sm">Pickup Rate</div>
            </div>
        </div>
    </div>

   <!-- Active Orders -->
<?php if(!empty($active_orders)): ?>
<div style="margin-bottom:28px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-family:var(--font-display);font-size:16px;font-weight:700;">Active Orders</span>
            <span style="width:8px;height:8px;background:var(--success);border-radius:50%;display:inline-block;animation:pulse 1.5s infinite;"></span>
        </div>
        <button onclick="location.reload()" style="font-size:13px;font-family:var(--font-body);font-weight:600;border:1.5px solid var(--border);background:none;padding:7px 14px;border-radius:8px;cursor:pointer;color:var(--text);">&#8635; Refresh</button>
    </div>
    <?php foreach($active_orders as $ao):
        $steps = ['Pending','Preparing','Ready','Completed'];
        $current_step = array_search($ao['status'], $steps);
        if($current_step === false) $current_step = 0;
        $badge_map = ['Pending'=>'badge-pending','Preparing'=>'badge-preparing','Ready'=>'badge-ready'];
        $badge = $badge_map[$ao['status']] ?? 'badge-pending';
        $msg_map = [
            'Ready'     => ['🎉 Your order is ready! Please collect it at the counter.','background:#e8f5ee;color:var(--success);'],
            'Preparing' => ['👨‍🍳 Your food is being prepared. Won\'t be long!','background:#dbeafe;color:#1e40af;'],
            'Pending'   => ['⏳ Order received. Canteen is reviewing it.','background:#fef3c7;color:#92400e;'],
        ];
        $msg = $msg_map[$ao['status']] ?? $msg_map['Pending'];
    ?>
    <div class="order-card animate-in" style="background:var(--bg-card);border-radius:var(--radius);border:1px solid var(--border);padding:24px;margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-muted);font-weight:600;margin-bottom:4px;">Token Number</div>
                <div style="font-family:var(--font-display);font-size:28px;font-weight:800;color:var(--primary);">#<?php echo $ao['token']??$ao['id']; ?></div>
            </div>
            <span class="badge <?php echo $badge; ?>"><?php echo $ao['status']; ?></span>
        </div>

        <!-- Tracker -->
        <div class="order-tracker" style="margin-bottom:20px;">
            <?php foreach($steps as $i => $step):
                $done   = $i < $current_step;
                $active = $i === $current_step;
            ?>
            <div class="tracker-step <?php echo $done?'done':($active?'active':''); ?>">
                <div class="tracker-dot"><?php echo $done?'✓':''; ?></div>
                <div class="tracker-label"><?php echo $step; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Meta -->
        <div style="display:flex;flex-wrap:wrap;gap:20px;padding:14px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:16px;">
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.7px;margin-bottom:4px;">Items</div>
                <div style="font-size:14px;font-weight:500;"><?php echo htmlspecialchars($ao['items_summary']); ?></div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.7px;margin-bottom:4px;">Pickup Slot</div>
                <div style="font-size:14px;font-weight:500;">🕐 <?php echo htmlspecialchars($ao['slot']??'—'); ?></div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.7px;margin-bottom:4px;">Total</div>
                <div style="font-family:var(--font-display);font-size:16px;font-weight:700;">₹<?php echo number_format($ao['total'],2); ?></div>
            </div>
            <div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.7px;margin-bottom:4px;">Ordered At</div>
                <div style="font-size:14px;font-weight:500;"><?php echo date('h:i A',strtotime($ao['created_at'])); ?></div>
            </div>
        </div>

        <div style="border-radius:10px;padding:12px 16px;font-size:14px;font-weight:600;<?php echo $msg[1]; ?>">
            <?php echo $msg[0]; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<div style="background:var(--bg-card);border-radius:var(--radius);border:1px solid var(--border);padding:32px;margin-bottom:28px;text-align:center;">
    <div style="font-size:32px;margin-bottom:8px;">🍽️</div>
    <div style="font-family:var(--font-display);font-size:15px;font-weight:700;margin-bottom:4px;">No Active Orders</div>
    <div style="font-size:13px;color:var(--text-muted);">Place an order from the menu and track it here!</div>
</div>
<?php endif; ?>

    <!-- Past Orders Table -->
    <div style="font-family:var(--font-display);font-size:16px;font-weight:700;margin-bottom:14px;">Past Orders</div>
    <?php if(!empty($display_orders)): ?>
    <div class="section-card" style="padding:0;overflow:hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Token</th>
                    <th>Items</th>
                    <th>Pickup Slot</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($display_orders as $order):
                    $badge = ['Completed'=>'badge-completed','Missed'=>'badge-missed'][$order['status']] ?? 'badge-completed';
                ?>
                <tr style="cursor:default;">
                    <td><strong style="font-family:var(--font-display);color:var(--primary);">#<?php echo $order['token']??$order['id']; ?></strong></td>
                    <td style="max-width:220px;font-size:13px;"><?php echo htmlspecialchars($order['items_summary']); ?></td>
                    <td style="font-size:13px;">🕐 <?php echo htmlspecialchars($order['slot']??'—'); ?></td>
                    <td><strong>₹<?php echo number_format($order['total'],2); ?></strong></td>
                    <td style="font-size:13px;color:var(--text-muted);"><?php echo date('d M Y',strtotime($order['created_at'])); ?></td>
                    <td><span class="badge <?php echo $badge; ?>"><?php echo $order['status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">🕐</div>
        <div class="empty-title">No history yet</div>
        <div class="empty-subtitle">Your completed orders will appear here.</div>
    </div>
    <?php endif; ?>

</main>

<!-- Zomato style order bar -->
<?php include('includes/order_bar.php'); ?>

</body>
</html>
