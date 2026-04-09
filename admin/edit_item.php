<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if(!$id) { header("Location: dashboard.php"); exit(); }

$success = '';
$error = '';

// Handle form submit
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = mysqli_real_escape_string($conn, trim($_POST['item_name'] ?? ''));
    $category  = mysqli_real_escape_string($conn, trim($_POST['category'] ?? ''));
    $price     = (float)($_POST['price'] ?? 0);
    $is_available = (int)($_POST['is_available'] ?? 1);
    $current_image = mysqli_real_escape_string($conn, $_POST['current_image'] ?? '');
    $image = $current_image;

    if(!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $filename = strtolower(str_replace(' ', '', $item_name)) . '.' . $ext;
            $upload_path = '../assets/images/' . $filename;
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $filename;
            }
        }
    }

    $result = mysqli_query($conn, "UPDATE menu SET item_name='$item_name', category='$category', 
                                   price=$price, image='$image', is_available=$is_available 
                                   WHERE item_id=$id");
    if($result) {
        $success = "✅ Item updated successfully!";
    } else {
        $error = 'Error: ' . mysqli_error($conn);
    }
}

// Fetch item
$item_q = mysqli_query($conn, "SELECT * FROM menu WHERE item_id=$id");
if(!$item_q || mysqli_num_rows($item_q) === 0) { header("Location: dashboard.php"); exit(); }
$item = mysqli_fetch_assoc($item_q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Edit Item</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
                .logo-text span {
    color: var(--primary); 
}

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
    <a class="nav-item active" href="dashboard.php">
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
            <h1 class="page-title">Edit Item</h1>
            
        </div>
        <a href="menu.php" style="text-decoration:none;font-size:13px;color:var(--text-muted);border:1.5px solid var(--border);padding:9px 16px;border-radius:var(--radius-sm);font-weight:600;">← Back</a>
    </div>

    <div style="max-width: 560px; margin: 40px auto 0 auto;">
        <?php if($success): ?>
        <div style="background:#e8f5ee;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px;">
            <?php echo $success; ?> <a href="dashboard.php" style="color:var(--primary);margin-left:10px;">View Menu →</a>
        </div>
        <?php endif; ?>
        <?php if($error): ?>
        <div style="background:#fde8e8;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px;">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="section-card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($item['image'] ?? ''); ?>">

                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-input" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-input" style="cursor:pointer;">
                            <?php
                            $cats = ['Snacks','Beverages','Meals','Parantha','South Indian','Naan Combos','Rice Combos','Chinese','Pasta','Sandwich & Roll','Maggi','Pattis','Protein Salads'];
                            foreach($cats as $c) {
                                $sel = ($item['category'] === $c) ? 'selected' : '';
                                echo "<option $sel>$c</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (₹)</label>
                        <input type="number" name="price" class="form-input" value="<?php echo $item['price']; ?>" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Availability</label>
                    <select name="is_available" class="form-input" style="cursor:pointer;">
                        <option value="1" <?php echo $item['is_available'] ? 'selected' : ''; ?>>Available</option>
                        <option value="0" <?php echo !$item['is_available'] ? 'selected' : ''; ?>>Not Available</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Item Image</label>
                    <?php if(!empty($item['image']) && file_exists('../assets/images/'.$item['image'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-input" accept="image/*" style="padding:8px;">
                    <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">Leave empty to keep current image</p>
                </div>
                <button type="submit" class="btn-primary-full" style="margin-top:8px;">Save Changes</button>
            </form>
        </div>
    </div>
</main>

<button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
</button>
<script>
function toggleDarkMode() { document.body.classList.toggle('dark-mode'); localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')); }
if(localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark-mode');


</script>
</body>
</html>
