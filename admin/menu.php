<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$category_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';
$query = "SELECT * FROM menu";
if($category_filter !== 'all') {
    $query .= " WHERE category = '$category_filter'";
}
$query .= " ORDER BY category, item_name";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Menu Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --sidebar-w: 240px; }
        .food-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px; }
        .food-card { position:relative; }
        .admin-card-actions {
            position: absolute;
            top: 10px; right: 10px;
            display: flex;
            gap: 6px;
            z-index: 10;
        }
        .card-action-btn {
            width: 30px; height: 30px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            transition: var(--transition);
            text-decoration: none;
        }
        .card-action-edit { background: rgba(255,255,255,0.9); color: var(--text); }
        .card-action-edit:hover { background: #fff; }
        .card-action-delete { background: rgba(254,226,226,0.9); color: #991b1b; }
        .card-action-delete:hover { background: #fde8e8; }
        .out-of-stock { opacity: 0.5; }
        .out-of-stock .food-card-img,
        .out-of-stock .food-card-img-placeholder { filter: grayscale(100%); }
        .out-of-stock-badge {
            position: absolute;
            top: 10px; left: 10px;
            background: #6b7280;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            z-index: 10;
        }
        .toggle-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
        }

                .logo-text span {
    color: var(--primary); 
}


    .confirm-modal { position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:600; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:opacity 0.3s; backdrop-filter:blur(4px); }
.confirm-modal.open { opacity:1; pointer-events:all; }
.confirm-box { background:#fff; border-radius:20px; padding:36px 32px; max-width:360px; width:90%; text-align:center; box-shadow:0 12px 48px rgba(0,0,0,0.2); }
.confirm-title { font-family:'Montserrat',sans-serif; font-size:20px; font-weight:800; margin-bottom:8px; }
.confirm-sub { font-size:14px; color:var(--text-muted); margin-bottom:28px; }
.confirm-actions { display:flex; gap:10px; }

    .confirm-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 600;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(4px);
}
.confirm-overlay.open { opacity: 1; pointer-events: all; }
.confirm-box {
    background: #fff;
    border-radius: 20px;
    padding: 36px 32px;
    max-width: 360px; width: 90%;
    text-align: center;
    box-shadow: 0 12px 48px rgba(0,0,0,0.2);
    transform: scale(0.9);
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.confirm-overlay.open .confirm-box { transform: scale(1); }


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
    background-color: #1e1e1e !important; 
    color: #ffffff !important; 
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
    
    <a class="nav-item" href="dashboard.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
    </a>
    
    <a class="nav-item active" href="menu.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        Manage Menu
    </a>

    <a class="nav-item" href="orders.php">
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
            <h1 class="page-title">Manage Menu</h1>
            <p class="page-subtitle">Toggle availability, edit or delete items</p>
        </div>
        <a href="add_item.php" style="text-decoration:none;background:var(--primary);color:#fff;padding:11px 20px;border-radius:var(--radius-sm);font-family:var(--font-body);font-size:14px;font-weight:600;">+ Add Item</a>
    </div>

    <!-- Filter Pills -->
    <div class="filter-bar" style="flex-wrap:wrap;margin-bottom:28px;">
        <?php
        $cats = ['all'=>'All','Snacks'=>'🥨 Snacks','Beverages'=>'☕ Beverages','Meals'=>'🍛 Meals','Parantha'=>'🫓 Parantha','South Indian'=>'🥞 South Indian','Naan Combos'=>'🫓 Naan','Rice Combos'=>'🍚 Rice','Chinese'=>'🍜 Chinese','Pasta'=>'🍝 Pasta','Sandwich & Roll'=>'🥪 Sandwich','Maggi'=>'🍜 Maggi','Pattis'=>'🍞 Pattis'];
        foreach($cats as $key => $label):
        ?>
        <a href="menu.php?cat=<?php echo urlencode($key); ?>" class="filter-pill <?php echo $category_filter===$key?'active':''; ?>" style="text-decoration:none;">
            <?php echo $label; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Food Grid -->
    <div class="food-grid">
        <?php
        $emoji_map = ['Snacks'=>'🥨','Beverages'=>'☕','Meals'=>'🍛','South Indian'=>'🥞','Chinese'=>'🍜','Pattis'=>'🍞','Naan Combos'=>'🫓','Rice Combos'=>'🍚','Pasta'=>'🍝','Sandwich & Roll'=>'🥪','Maggi'=>'🍜','Parantha'=>'🫓'];
        if($result && mysqli_num_rows($result) > 0):
            while($row = mysqli_fetch_assoc($result)):
                $emoji = $emoji_map[$row['category']] ?? '🍽️';
                $available = (int)$row['is_available'];
        ?>
        <div class="food-card <?php echo !$available ? 'out-of-stock' : ''; ?>">

            <!-- Out of stock badge -->
            <?php if(!$available): ?>
            <div class="out-of-stock-badge">Out of Stock</div>
            <?php endif; ?>

            <!-- Edit + Delete buttons -->

     <?php if(isset($_SESSION['delete_success'])): ?>
    <div id="delete-alert" style="
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        background: #fde8e8; 
        color: #9b1c1c; 
        padding: 14px 24px; 
        border-radius: 12px; 
        border: 1.5px solid #f8b4b4;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);">
        <span style="font-size: 18px;">🗑️</span>
        <span>Item deleted successfully!</span>
    </div>

    <style>
        @keyframes slideDown {
            from { top: -100px; opacity: 0; }
            to { top: 20px; opacity: 1; }
        }
    </style>

    <script>
        // 4 second fadeout
        setTimeout(() => {
            const alert = document.getElementById('delete-alert');
            if(alert) {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            }
        }, 4000);
    </script>

    <?php unset($_SESSION['delete_success']); ?>
<?php endif; ?>

            <div class="admin-card-actions">
                <a href="edit_item.php?id=<?php echo $row['item_id']; ?>" class="card-action-btn card-action-edit" title="Edit">✏️</a>
                <a href="delete_item.php?id=<?php echo $row['item_id']; ?>" 
                   onclick="confirmDelete(<?php echo $row['item_id']; ?>, '<?php echo addslashes($row['item_name']); ?>')"
                   class="card-action-btn card-action-delete" title="Delete">🗑️</a>
            </div>

            <?php if(!empty($row['image']) && file_exists('../assets/images/'.$row['image'])): ?>
            <img class="food-card-img" src="../assets/images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
            <?php else: ?>
            <div class="food-card-img-placeholder"><?php echo $emoji; ?></div>
            <?php endif; ?>

            <div class="food-card-body">
                <div class="food-card-category"><?php echo htmlspecialchars($row['category']); ?></div>
                <div class="food-card-name"><?php echo htmlspecialchars($row['item_name']); ?></div>
                <div class="food-card-footer" style="margin-top:12px;">
                    <span class="food-price">₹<?php echo number_format($row['price'],2); ?></span>
                    <div class="toggle-wrap">
                        <?php echo $available ? 'Available' : 'Unavailable'; ?>
                        <label class="toggle-switch">
                            <input type="checkbox" <?php echo $available ? 'checked' : ''; ?>
                                   onchange="toggleItem(<?php echo $row['item_id']; ?>, this.checked, this)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="empty-state" style="grid-column:1/-1;">
            <div class="empty-icon">🍽️</div>
            <div class="empty-title">No items found</div>
        </div>
        <?php endif; ?>
    </div>

            <div class="confirm-modal" id="confirm-modal">
    <div class="confirm-box">
        <div style="font-size:48px;margin-bottom:16px;">🗑️</div>
        <div class="confirm-title">Delete Item?</div>
        <div class="confirm-sub" id="confirm-msg">Are you sure you want to delete this item?</div>
        <div class="confirm-actions">
            <button class="btn-outline" onclick="closeConfirm()">Cancel</button>
            <a id="confirm-yes" href="#" style="flex:1;padding:12px;border:none;border-radius:10px;background:#ef4444;color:#fff;font-family:var(--font-body);font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;display:flex;align-items:center;justify-content:center;">Delete</a>
        </div>
    </div>
</div>
<div style="font-size: 12px; color: #888885; margin-top: 40px; text-align: center; display: block; width: 100%; font-family: 'Inter', sans-serif; padding-bottom: 20px; clear: both;">
    Foodly v1.0 &nbsp;·&nbsp; College Canteen
</div>
</main>
            <div class="confirm-overlay" id="confirm-overlay">
    <div class="confirm-box">
        <div style="width:56px;height:56px;background:#fde8e8;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#991b1b" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <div style="font-family:'Montserrat',sans-serif;font-size:20px;font-weight:800;margin-bottom:8px;">Delete Item?</div>
        <div style="font-size:14px;color:var(--text-muted);margin-bottom:28px;" id="confirm-item-name">This action cannot be undone.</div>
        <div style="display:flex;gap:10px;">
            <button onclick="closeConfirm()" style="flex:1;padding:12px;border:1.5px solid var(--border);border-radius:10px;background:none;font-family:var(--font-body);font-size:14px;font-weight:600;cursor:pointer;color:var(--text);">Cancel</button>
            <a id="confirm-delete-btn" href="#" style="flex:1;padding:12px;border:none;border-radius:10px;background:#ef4444;color:#fff;font-family:var(--font-body);font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;display:flex;align-items:center;justify-content:center;">Delete</a>
        </div>
    </div>
</div>
<script>
function confirmDelete(id, name) {
    document.getElementById('confirm-item-name').textContent = 'Delete "' + name + '"? This cannot be undone.';
    document.getElementById('confirm-delete-btn').href = 'delete_item.php?id=' + id;
    document.getElementById('confirm-overlay').classList.add('open');
}
function closeConfirm() {
    document.getElementById('confirm-overlay').classList.remove('open');
}

function toggleItem(itemId, available, checkbox) {
    fetch('toggle_item.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `item_id=${itemId}&available=${available ? 1 : 0}`
    }).then(r => r.json()).then(d => {
        if(d.success) {
            // Toggle out-of-stock class on card
            const card = checkbox.closest('.food-card');
            card.classList.toggle('out-of-stock', !available);

            // Update label
            const label = card.querySelector('.toggle-wrap');
            label.childNodes[0].textContent = available ? 'Available' : 'Unavailable';
            
            // Toggle badge
            let badge = card.querySelector('.out-of-stock-badge');
            if(!available && !badge) {
                badge = document.createElement('div');
                badge.className = 'out-of-stock-badge';
                badge.textContent = 'Out of Stock';
                card.appendChild(badge);
            } else if(available && badge) {
                badge.remove();
            }
        } else {
            checkbox.checked = !available; // revert
            alert('Failed to update!');
        }
    });
}
</script>
<button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
</button>
<script>
function toggleDarkMode() { document.body.classList.toggle('dark-mode'); localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')); }
if(localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark-mode');



</script>
</body>
</html>
