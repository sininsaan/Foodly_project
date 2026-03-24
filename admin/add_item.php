<?php
session_start();
include('../includes/db.php');

if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = mysqli_real_escape_string($conn, trim($_POST['item_name'] ?? ''));
    $category  = mysqli_real_escape_string($conn, trim($_POST['category'] ?? ''));
    $price     = (float)($_POST['price'] ?? 0);
    $image     = '';

    if(empty($item_name) || empty($category) || $price <= 0) {
        $error = 'Please fill all fields correctly.';
    } else {
        // Handle image upload
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

        $result = mysqli_query($conn, "INSERT INTO menu (item_name, category, price, image, is_available) 
                                       VALUES ('$item_name', '$category', $price, '$image', 1)");
        if($result) {
            $success = "✅ '$item_name' added successfully!";
        } else {
            $error = 'Database error: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Add Menu Item</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
    <a class="nav-item active" href="dashboard.php">
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
            <h1 class="page-title">Add Menu Item</h1>
            <p class="page-subtitle">Add a new item to the canteen menu</p>
        </div>
        <a href="dashboard.php" style="text-decoration:none;font-size:13px;color:var(--text-muted);border:1.5px solid var(--border);padding:9px 16px;border-radius:var(--radius-sm);font-weight:600;">← Back</a>
    </div>

    <div style="max-width:560px;">
        <?php if($success): ?>
        <div style="background:#e8f5ee;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px;">
            <?php echo $success; ?>
            <a href="dashboard.php" style="color:var(--primary);margin-left:10px;">View Menu →</a>
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div style="background:#fde8e8;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px;">
            ❌ <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <div class="section-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-input" placeholder="e.g. Veg Samosa" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-input" style="cursor:pointer;">
                            <option>Snacks</option>
                            <option>Beverages</option>
                            <option>Meals</option>
                            <option>Parantha</option>
                            <option>South Indian</option>
                            <option>Naan Combos</option>
                            <option>Rice Combos</option>
                            <option>Chinese</option>
                            <option>Pasta</option>
                            <option>Sandwich & Roll</option>
                            <option>Maggi</option>
                            <option>Pattis</option>
                            <option>Protein Salads</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (₹)</label>
                        <input type="number" name="price" class="form-input" placeholder="e.g. 15" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Item Image (optional)</label>
                    <input type="file" name="image" class="form-input" accept="image/*" style="padding:8px;">
                </div>
                <button type="submit" class="btn-primary-full" style="margin-top:8px;">Add Item</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>
