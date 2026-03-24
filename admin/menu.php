<?php
session_start();
include('../includes/db.php');

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
    <a class="nav-item" href="orders.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        All Orders
    </a>
    <a class="nav-item active" href="menu.php">
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
            <div class="admin-card-actions">
                <a href="edit_item.php?id=<?php echo $row['item_id']; ?>" class="card-action-btn card-action-edit" title="Edit">✏️</a>
                <a href="delete_item.php?id=<?php echo $row['item_id']; ?>"
                   onclick="return confirm('Delete <?php echo addslashes($row['item_name']); ?>?')"
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
</main>

<script>
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
</body>
</html>
