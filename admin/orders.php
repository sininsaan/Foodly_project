<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

// Filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : 'all';
$date_filter = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : 'today';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

// Build query
$where = [];
if($date_filter === 'today') $where[] = "DATE(o.created_at) = CURDATE()";
elseif($date_filter === 'yesterday') $where[] = "DATE(o.created_at) = CURDATE() - INTERVAL 1 DAY";
elseif($date_filter === 'week') $where[] = "o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";

if($status_filter !== 'all') $where[] = "o.status = '$status_filter'";
if(!empty($search)) $where[] = "(o.roll_no LIKE '%$search%' OR o.token LIKE '%$search%')";

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$orders_q = mysqli_query($conn, "SELECT o.*, 
    GROUP_CONCAT(CONCAT(oi.item_name,' x',oi.quantity) SEPARATOR ', ') as items,
    ts.label as slot
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN time_slots ts ON o.slot_id = ts.id
    $where_sql
    GROUP BY o.id 
    ORDER BY o.created_at DESC
    LIMIT 100");

// Count stats
$total_count = 0;
$orders_data = [];
if($orders_q) {
    while($r = mysqli_fetch_assoc($orders_q)) {
        $orders_data[] = $r;
        $total_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | All Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --sidebar-w: 240px; }
        .filter-select {
            padding: 9px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            background: #fff;
            cursor: pointer;
            color: var(--text);
            outline: none;
        }
        .filter-select:focus { border-color: var(--text); }
        .search-input-sm {
            padding: 9px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 13px;
            background: #fff;
            outline: none;
            color: var(--text);
            width: 200px;
        }
        .search-input-sm:focus { border-color: var(--text); }
        .status-select { font-size:12px; font-family:var(--font-body); font-weight:600; border:1.5px solid var(--border); border-radius:6px; padding:5px 8px; background:#fff; cursor:pointer; color:var(--text); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🍽️</div>
        <span class="logo-text">Foodly</span>
    </div>
    <p class="nav-label">Admin Panel</p>
    <a class="nav-item" href="dashboard.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
    </a>
    <a class="nav-item active" href="orders.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        All Orders
    </a>

    <a class="nav-item" href="menu.php">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
    Manage Menu
</a>
    <div class="sidebar-footer">
        <div class="user-pill">
            <div class="user-avatar">A</div>
            <div class="user-info">
                <div class="user-name">Canteen Admin</div>
                <div class="user-roll">DAV College</div>
            </div>
        </div>
        <a class="nav-item" href="logout.php" style="margin-top:8px;">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Logout
        </a>
    </div>
</aside>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">All Orders</h1>
            <p class="page-subtitle"><?php echo $total_count; ?> orders found</p>
        </div>
        <a href="dashboard.php" style="text-decoration:none;font-size:13px;color:var(--text-muted);border:1.5px solid var(--border);padding:9px 16px;border-radius:var(--radius-sm);font-weight:600;">← Dashboard</a>
    </div>

    <!-- Filters -->
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:24px;align-items:center;">
        <select name="date" class="filter-select" onchange="this.form.submit()">
            <option value="today" <?php echo $date_filter==='today'?'selected':''; ?>>Today</option>
            <option value="yesterday" <?php echo $date_filter==='yesterday'?'selected':''; ?>>Yesterday</option>
            <option value="week" <?php echo $date_filter==='week'?'selected':''; ?>>Last 7 Days</option>
            <option value="all" <?php echo $date_filter==='all'?'selected':''; ?>>All Time</option>
        </select>

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="all" <?php echo $status_filter==='all'?'selected':''; ?>>All Status</option>
            <option value="Pending" <?php echo $status_filter==='Pending'?'selected':''; ?>>Pending</option>
            <option value="Preparing" <?php echo $status_filter==='Preparing'?'selected':''; ?>>Preparing</option>
            <option value="Ready" <?php echo $status_filter==='Ready'?'selected':''; ?>>Ready</option>
            <option value="Completed" <?php echo $status_filter==='Completed'?'selected':''; ?>>Completed</option>
            <option value="Missed" <?php echo $status_filter==='Missed'?'selected':''; ?>>Missed</option>
        </select>

        <input type="text" name="search" class="search-input-sm" placeholder="Search roll no / token..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" style="padding:9px 16px;background:var(--text);color:#fff;border:none;border-radius:var(--radius-sm);font-family:var(--font-body);font-size:13px;font-weight:600;cursor:pointer;">Search</button>
        <?php if(!empty($search) || $status_filter !== 'all'): ?>
        <a href="orders.php?date=<?php echo $date_filter; ?>" style="font-size:13px;color:var(--text-muted);font-weight:600;text-decoration:none;">✕ Clear</a>
        <?php endif; ?>
    </form>

    <!-- Orders Table -->
    <div class="section-card" style="padding:0;overflow:hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Token</th>
                    <th>Roll No</th>
                    <th>Items</th>
                    <th>Slot</th>
                    <th>Amount</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($orders_data)): ?>
                <?php foreach($orders_data as $ord):
                    $badge_map = ['Pending'=>'badge-pending','Preparing'=>'badge-preparing','Ready'=>'badge-ready','Completed'=>'badge-completed','Missed'=>'badge-missed'];
                    $badge = $badge_map[$ord['status']] ?? 'badge-pending';
                ?>
                <tr id="order-row-<?php echo $ord['id']; ?>">
                    <td><strong style="font-family:var(--font-display);color:var(--primary);">#<?php echo $ord['token']??$ord['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($ord['roll_no']); ?></td>
                    <td style="max-width:220px;font-size:13px;"><?php echo htmlspecialchars($ord['items'] ?? '—'); ?></td>
                    <td style="font-size:12px;color:var(--text-muted);">🕐 <?php echo htmlspecialchars($ord['slot']??'—'); ?></td>
                    <td><strong>₹<?php echo number_format($ord['total'],2); ?></strong></td>
                    <td style="font-size:12px;color:var(--text-muted);"><?php echo date('d M, h:i A',strtotime($ord['created_at'])); ?></td>
                    <td>
                        <select class="status-select" onchange="updateStatus(<?php echo $ord['id']; ?>, this.value)">
                            <?php foreach(['Pending','Preparing','Ready','Completed','Missed'] as $st): ?>
                            <option <?php echo $ord['status']===$st?'selected':''; ?>><?php echo $st; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="7" style="text-align:center;padding:48px;color:var(--text-muted);">
                    <div style="font-size:36px;margin-bottom:12px;">📋</div>
                    No orders found for selected filters.
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function updateStatus(orderId, status) {
    fetch('update_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${orderId}&status=${encodeURIComponent(status)}`
    }).then(r => r.json()).then(d => {
        if(d.success) {
            const row = document.getElementById('order-row-' + orderId);
            if(row) { row.style.background = '#e8f5ee'; setTimeout(() => row.style.background = '', 1000); }
        }
    });
}
</script>
</body>
</html>
