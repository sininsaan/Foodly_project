<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

// Stats
$total_orders = 0; $pending = 0; $revenue = 0; $ready = 0;
$stats_q = mysqli_query($conn, "SELECT status, COUNT(*) as cnt, SUM(total) as rev FROM orders WHERE DATE(created_at)=CURDATE() GROUP BY status");
if($stats_q) {
    while($r = mysqli_fetch_assoc($stats_q)) {
        $total_orders += $r['cnt'];
        if($r['status']==='Pending') $pending = $r['cnt'];
        if($r['status']==='Ready') $ready = $r['cnt'];
        $revenue += $r['rev'];
    }
}

// Today's orders
$orders_q = mysqli_query($conn, "SELECT o.*, GROUP_CONCAT(CONCAT(oi.item_name,' x',oi.quantity) SEPARATOR ', ') as items,
             ts.label as slot
             FROM orders o 
             LEFT JOIN order_items oi ON o.id=oi.order_id
             LEFT JOIN time_slots ts ON o.slot_id=ts.id
             WHERE DATE(o.created_at)=CURDATE()
             GROUP BY o.id ORDER BY o.created_at DESC");

// Menu items
$menu_q = mysqli_query($conn, "SELECT * FROM menu ORDER BY category, item_name");

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --sidebar-w: 240px; }
        .admin-tab-bar { display:flex; gap:4px; background:var(--bg); border-radius:var(--radius-sm); padding:4px; margin-bottom:28px; max-width:300px; }
        .admin-tab { flex:1; padding:9px 14px; border:none; background:none; border-radius:8px; font-family:var(--font-body); font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); color:var(--text-muted); }
        .admin-tab.active { background:#fff; color:var(--text); box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        .tab-panel { display:none; }
        .tab-panel.active { display:block; }
        .status-select { font-size:12px; font-family:var(--font-body); font-weight:600; border:1.5px solid var(--border); border-radius:6px; padding:5px 8px; background:#fff; cursor:pointer; color:var(--text); }
        .status-select:focus { outline:none; border-color:var(--text); }

        .logo-text span {
    color: var(--primary); 
}

 /* Admin buttons dark mode visibility fix */
body.dark-mode .admin-btn, 
body.dark-mode button[type="submit"],
body.dark-mode .action-btn {
    background-color: var(--primary) !important; 
    color: #ffffff !important;
    opacity: 1 !important;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

body.dark-mode .btn-delete {
    background-color: #ff4757 !important; 
    opacity: 1 !important;
}

body.dark-mode .admin-table td {
    color: var(--text); 
}
 /* --- ADMIN DARK MODE FIX: BUTTONS & INPUTS --- */

body.dark-mode .status-btn, 
body.dark-mode .badge,
body.dark-mode .btn-ready,
body.dark-mode .btn-preparing,
body.dark-mode button[type="submit"] {
    background-color: var(--primary) !important; 
    color: #ffffff !important; 
    border: none !important;
}

body.dark-mode input, 
body.dark-mode select, 
body.dark-mode .search-box {
    background-color: #1e1e1e !important; /* Dark background */
    color: #ffffff !important; /* White text */
    border: 1px solid #333 !important;
}


body.dark-mode .data-table td {
    color: #e0e0e0 !important;
}

/* Specific button colors for Status */
body.dark-mode .badge-ready { background-color: #2d9a5f !important; }
body.dark-mode .badge-pending { background-color: #f39c12 !important; }

/* Dashboard cards fix */
body.dark-mode .section-card,
body.dark-mode .stat-card-sm {
    background-color: #181818 !important;
    border: 1px solid #222 !important;
}
    </style>
</head>
<body>

<aside class="sidebar">
   <div class="sidebar-logo">
       <a href="logout.php" style = "text-decoration:none;"> <div class="logo-icon">🍽️</div></a>
        <a href="logout.php" style="text-decoration:none;"><span class="logo-text">Food<span>ly</span></span></a>
    </div>
    
    <a class="nav-item active" href="dashboard.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
    </a>

    
    <a class="nav-item" href="menu.php">
    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
    Manage Menu
</a>
    <a class="nav-item" href="orders.php" onclick="switchTab('orders');return false;">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        All Orders
    </a>
     <a class="nav-item" href="users.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
        Manage Users
    </a>
   
    <div class="sidebar-footer">
        <div class="user-pill">
            <div class="user-avatar">A</div>
            <div class="user-info">
                <div class="user-name">Canteen Admin</div>
                <div class="user-roll">College</div>
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
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="page-subtitle">📅 <?php echo date('l, d F Y'); ?> &nbsp;·&nbsp; College Canteen</p>
        </div>
        <a href="#" style="padding: 9px 16px; width: 120px; visibility: hidden;"></a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card" style="pointer-events:none;">
            <div class="stat-icon orange">📋</div>
            <div class="stat-value"><?php echo $total_orders ?: 0; ?></div>
            <div class="stat-label">Today's Orders</div>
        </div>
        <div class="stat-card" style="pointer-events:none;">
            <div class="stat-icon purple">⏳</div>
            <div class="stat-value"><?php echo $pending ?: 0; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card" style="pointer-events:none;">
            <div class="stat-icon green">✅</div>
            <div class="stat-value"><?php echo $ready ?: 0; ?></div>
            <div class="stat-label">Ready for Pickup</div>
        </div>
        <div class="stat-card" style="pointer-events:none;">
            <div class="stat-icon blue">💰</div>
            <div class="stat-value">₹<?php echo number_format($revenue ?: 0); ?></div>
            <div class="stat-label">Today's Revenue</div>
        </div>
    </div>


    <!-- Peak Hour Insight -->
    <div class="section-card" style="margin-bottom:24px;">
        <div class="section-card-header">
            <span class="section-card-title">📊 Peak Hour Insight</span>
            <span style="font-size:12px;color:var(--text-muted);">Today's orders by slot</span>
        </div>
        <?php
        $peak_q = mysqli_query($conn, "SELECT ts.label, COUNT(o.id) as cnt FROM orders o LEFT JOIN time_slots ts ON o.slot_id = ts.id WHERE DATE(o.created_at) = CURDATE() GROUP BY o.slot_id ORDER BY cnt DESC");
        $peak_data = [];
        $peak_max = 1;
        if($peak_q) { while($p = mysqli_fetch_assoc($peak_q)) { $peak_data[] = $p; if($p['cnt'] > $peak_max) $peak_max = $p['cnt']; } }
        if(empty($peak_data)):
        ?>
        <p style="font-size:13px;color:var(--text-muted);">No orders today yet.</p>
        <?php else: foreach($peak_data as $p): $pct = round(($p['cnt']/$peak_max)*100); ?>
        <div class="peak-bar-wrap">
            <div class="peak-bar-label"><?php echo htmlspecialchars($p['label'] ?? 'Unknown'); ?></div>
            <div style="flex:1;background:var(--border);border-radius:10px;height:8px;">
                <div class="peak-bar" style="width:<?php echo $pct; ?>%;"></div>
            </div>
            <div class="peak-bar-count"><?php echo $p['cnt']; ?> orders</div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Tabs -->
<div style="font-family:var(--font-display);font-size:16px;font-weight:700;margin-bottom:20px;">📋 Live Orders</div>

    <!-- ORDERS TAB -->
    <div class="tab-panel active" id="tab-orders">
        <div class="section-card" style="padding:0;overflow:hidden;">
            <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <span style="font-family:var(--font-display);font-size:16px;font-weight:700;">Today's Orders</span>
                <button onclick="location.reload()" style="font-size:12px;font-family:var(--font-body);font-weight:600;border:1.5px solid var(--border);background:none;padding:6px 12px;border-radius:6px;cursor:pointer;">↻ Refresh</button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Roll No</th>
                        <th>Items</th>
                        <th>Slot</th>
                        <th>Amount</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id='orders-tbody'>
                    <?php
                    if($orders_q && mysqli_num_rows($orders_q) > 0) {
                        while($ord = mysqli_fetch_assoc($orders_q)):
                    ?>
                    <tr id="order-row-<?php echo $ord['id']; ?>">
                        <td><strong style="font-family:var(--font-display);color:var(--primary);">#<?php echo $ord['token']??$ord['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($ord['roll_no']); ?></td>
                        <td style="max-width:200px;font-size:13px;"><?php echo htmlspecialchars($ord['items'] ?? '—'); ?></td>
                        <td style="font-size:12px;color:var(--text-muted);">🕐 <?php echo htmlspecialchars($ord['slot']??'—'); ?></td>
                        <td><strong>₹<?php echo number_format($ord['total'],2); ?></strong></td>
                        <td style="font-size:12px;color:var(--text-muted);"><?php echo date('h:i A',strtotime($ord['created_at'])); ?></td>
                        <td>
                            <select class="status-select" onchange="updateOrderStatus(<?php echo $ord['id']; ?>, this.value)">
                                <?php foreach(['Pending','Preparing','Ready','Completed','Missed'] as $st): ?>
                                <option <?php echo $ord['status']===$st?'selected':''; ?>><?php echo $st; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endwhile; } else { ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No orders today yet.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MENU TAB REMOVED -->
    <div class="tab-panel" id="tab-menu" style="display:none;">
        <div class="section-card">
            <div class="section-card-header">
                <span class="section-card-title">Menu Items</span>
                <a href="add_item.php" class="add-item-btn" style="text-decoration:none;background:var(--primary);color:#fff;border-radius:var(--radius-sm);padding:10px 20px;font-family:var(--font-body);font-size:13px;font-weight:600;">+ Add Item</a>
            </div>

            <?php
            $cat_emojis = ['Snacks'=>'🥨','Beverages'=>'☕','Meals'=>'🍛','South Indian'=>'🥞','Chinese'=>'🍜','Naan Combos'=>'🫓','Rice Combos'=>'🍚','Pasta'=>'🍝','Sandwich & Roll'=>'🥪','Maggi'=>'🍜','Parantha'=>'🫓','Pattis'=>'🍞','Protein Salads'=>'🥗'];
            if($menu_q && mysqli_num_rows($menu_q) > 0) {
                while($item = mysqli_fetch_assoc($menu_q)):
                    $emoji = $cat_emojis[$item['category']] ?? '🍽️';
            ?>
            <div class="admin-menu-item">
                <div class="admin-item-info">
                    <div class="admin-item-thumb">
                        <?php if(!empty($item['image']) && file_exists('../assets/images/'.$item['image'])): ?>
                        <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="">
                        <?php else: ?>
                        <?php echo $emoji; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="admin-item-name"><?php echo htmlspecialchars($item['item_name']); ?></div>
                        <div class="admin-item-cat"><?php echo htmlspecialchars($item['category']); ?> &nbsp;·&nbsp; ₹<?php echo number_format($item['price'],2); ?></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:16px;">
                    <label class="toggle-switch">
                        <input type="checkbox" <?php echo $item['is_available']?'checked':''; ?>
                               onchange="toggleItem(<?php echo $item['item_id']; ?>, this.checked)">
                        <span class="toggle-slider"></span>
                    </label>
                    <a href="edit_item.php?id=<?php echo $item['item_id']; ?>" style="font-size:12px;border:1.5px solid var(--border);background:none;padding:5px 10px;border-radius:6px;cursor:pointer;font-family:var(--font-body);font-weight:600;text-decoration:none;color:var(--text);">Edit</a>
<a href="delete_item.php?id=<?php echo $item['item_id']; ?>" onclick="return confirm('Delete <?php echo addslashes($item['item_name']); ?>?')" style="font-size:12px;border:1.5px solid #fca5a5;background:none;padding:5px 10px;border-radius:6px;cursor:pointer;font-family:var(--font-body);font-weight:600;text-decoration:none;color:#991b1b;">Delete</a>
                </div>
            </div>
            <?php endwhile; } ?>
        </div>
    </div>

<div style="font-size: 12px; color: #888885; margin-top: 40px; text-align: center; display: block; width: 100%; font-family: 'Inter', sans-serif; padding-bottom: 20px; clear: both;">
    Foodly v1.0 &nbsp;·&nbsp; College Canteen
</div>
</main>

<script>
function switchTab(tab) {
    document.getElementById('tab-orders').classList.toggle('active', tab==='orders');
    document.getElementById('tab-menu').classList.toggle('active', tab==='menu');
    document.getElementById('tab-btn-orders').classList.toggle('active', tab==='orders');
    document.getElementById('tab-btn-menu').classList.toggle('active', tab==='menu');
}

function updateOrderStatus(orderId, status) {
    fetch('update_status.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`order_id=${orderId}&status=${encodeURIComponent(status)}`
    }).then(r=>r.json()).then(d=>{
        if(d.success) {
    const row = document.getElementById('order-row-'+orderId);
    if(row) {
        const color = status === 'Missed' ? '#fde8e8' : '#e8f5ee';
        row.style.background = color;
        setTimeout(() => row.style.background = '', 1200);
    }
}
    });
}

function toggleItem(itemId, available) {
    fetch('toggle_item.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`item_id=${itemId}&available=${available?1:0}`
    }).then(r=>r.json()).then(d=>{
        if(!d.success) alert('Failed to update item!');
    });
}
</script>
<button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
</button>
<script>
function toggleDarkMode() { document.body.classList.toggle('dark-mode'); localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')); }
if(localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark-mode');

// Auto-refresh orders table every 10 seconds
function refreshOrders() {
    fetch('get_live_orders.php')
        .then(r => r.json())
        .then(data => {
            if(!data.orders) return;
            const tbody = document.querySelector('#orders-tbody');
            if(!tbody) return;
            data.orders.forEach(order => {
                const row = document.getElementById('order-row-' + order.id);
                if(!row) {
                    // New order — add row
                    const tr = document.createElement('tr');
                    tr.id = 'order-row-' + order.id;
                    tr.style.animation = 'fadeInUp 0.4s ease';
                    tr.innerHTML = buildOrderRow(order);
                    tbody.insertBefore(tr, tbody.firstChild);
                }
            });
        }).catch(() => {});
}

function buildOrderRow(o) {
    return `<td><strong style="font-family:var(--font-display);color:var(--primary);">#${o.token||o.id}</strong></td>
            <td>${o.roll_no}</td>
            <td style="max-width:200px;font-size:13px;">${o.items||'—'}</td>
            <td style="font-size:12px;color:var(--text-muted);">🕐 ${o.slot||'—'}</td>
            <td><strong>₹${parseFloat(o.total).toFixed(2)}</strong></td>
            <td style="font-size:12px;color:var(--text-muted);">${o.time}</td>
            <td><select class="status-select" onchange="updateOrderStatus(${o.id}, this.value)">
                ${['Pending','Preparing','Ready','Completed','Missed'].map(s => 
                    `<option ${o.status===s?'selected':''}>${s}</option>`).join('')}
            </select></td>`;
}

setInterval(refreshOrders, 10000);



</script>
</body>
</html>
