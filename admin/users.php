<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

// Admin login check
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }

// Filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

// Build query
$where = [];
if($status_filter === 'suspended') $where[] = "status = 'suspended'";
elseif($status_filter === 'active') $where[] = "(status IS NULL OR status = 'active')";

if(!empty($search)) $where[] = "(roll_no LIKE '%$search%' OR name LIKE '%$search%')";

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$users_q = mysqli_query($conn, "SELECT * FROM users $where_sql ORDER BY roll_no ASC");

$total_users = mysqli_num_rows($users_q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --sidebar-w: 240px; }
        .filter-select, .search-input-sm {
            padding: 9px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            background: #fff;
            color: var(--text);
            outline: none;
        }
        .btn-reactivate {
            padding: 6px 12px;
            background: var(--text);
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-reactivate:hover { background: var(--primary); transform: translateY(-1px); }
        
        /* Status Badges */
        .badge-active { background: #e8f5ee; color: #065f46; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-suspended { background: #fde8e8; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }

        .logo-text span { color: var(--primary); }
        
        /* Dark Mode Fixes */
        body.dark-mode .filter-select, body.dark-mode .search-input-sm {
            background-color: #1e1e1e !important;
            color: #ffffff !important;
            border: 1px solid #333 !important;
        }

        /* Dark Mode Fixes for Buttons & Badges */
body.dark-mode .btn-reactivate {
    background-color: var(--primary) !important;
    color: #ffffff !important;
    border: none;
}

body.dark-mode .badge-active {
    background-color: #065f46 !important; 
    color: #34d399 !important; 
}

body.dark-mode .badge-suspended {
    background-color: #7f1d1d !important; 
    color: #fca5a5 !important; 
}
body.dark-mode .filter-select, 
body.dark-mode .search-input-sm {
    background-color: #1e1e1e !important;
    color: #ffffff !important;
    border: 1px solid #444 !important;
}

/* Sidebar active item fix */
body.dark-mode .nav-item.active {
    background-color: var(--primary) !important;
    color: #fff !important;
}
/* Specific Fix for Search Button and Input in Dark Mode */
body.dark-mode .search-input-sm {
    background-color: #2a2a2a !important;
    color: #ffffff !important;
    border: 1.5px solid #444 !important;
}

body.dark-mode button[type="submit"] {
    background-color: var(--primary) !important;
    color: #ffffff !important;
    border: none !important;
    cursor: pointer;
    transition: 0.3s ease;
}

body.dark-mode button[type="submit"]:hover {
    filter: brightness(1.2);
    transform: translateY(-1px);
}

body.dark-mode .btn-reactivate {
    background-color: #ffffff !important;
    color: #000000 !important;
}
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="dashboard.php" style="text-decoration:none;"> <div class="logo-icon">🍽️</div></a>
        <a href="dashboard.php" style="text-decoration:none;"><span class="logo-text">Food<span>ly</span></span></a>
    </div>
    
    <a class="nav-item" href="dashboard.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
    </a>

    <a class="nav-item" href="menu.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        Manage Menu
    </a>

    <a class="nav-item" href="orders.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        All Orders
    </a>

    <a class="nav-item active" href="users.php">
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
            <h1 class="page-title">Manage Users</h1>
        </div>
        <a href="dashboard.php" style="text-decoration:none;font-size:13px;color:var(--text-muted);border:1.5px solid var(--border);padding:9px 16px;border-radius:var(--radius-sm);font-weight:600;">← Dashboard</a>
    </div>

    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:24px;align-items:center;">
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="all" <?php echo $status_filter==='all'?'selected':''; ?>>All Users</option>
            <option value="active" <?php echo $status_filter==='active'?'selected':''; ?>>Active Only</option>
            <option value="suspended" <?php echo $status_filter==='suspended'?'selected':''; ?>>Suspended Only</option>
        </select>

        <input type="text" name="search" class="search-input-sm" placeholder="Search roll no / name..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" style="padding:9px 16px;background:var(--text);color:#fff;border:none;border-radius:var(--radius-sm);font-family:var(--font-body);font-size:13px;font-weight:600;cursor:pointer;">Search</button>
    </form>

    <div class="section-card" style="padding:0;overflow:hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($users_q)): 
                    $is_suspended = ($user['status'] === 'suspended');
                ?>
                <tr>
                    <td><strong style="color:var(--primary);"><?php echo $user['roll_no']; ?></strong></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td style="font-size:12px;"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td style="font-size:12px;"><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td>
                        <span class="<?php echo $is_suspended ? 'badge-suspended' : 'badge-active'; ?>">
                            <?php echo $is_suspended ? 'SUSPENDED' : 'ACTIVE'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if($is_suspended): ?>
                            <a href="#" 
                            class="btn-reactivate reactivate-btn" 
                            data-roll="<?php echo $user['roll_no']; ?>">
                         Reactivate
                            </a>
                        <?php else: ?>
                            <a href="#" class="btn-deactivate deactivate-btn" 
           data-roll="<?php echo $user['roll_no']; ?>" 
           style="background:#333; color:#fff; padding:6px 12px; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600;">
           Deactivate
        </a>
                            
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
  <div style="
    font-size: 12px; 
    color: var(--text-muted); 
    margin-top: 40px; 
    text-align: center; 
    display: block; 
    width: calc(100% - 260px); /* Sidebar ki width minus kar di */
    margin-left: 260px;        /* Itna right mein push kar diya */
    font-family: 'Inter', sans-serif; 
    padding-bottom: 20px; 
    clear: both;
    opacity: 0.8;">
    Foodly v1.0 &nbsp;·&nbsp; College Canteen
</div>
<button class="dark-toggle" onclick="toggleDarkMode()">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
</button>

<script>
function toggleDarkMode() { 
    document.body.classList.toggle('dark-mode'); 
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')); 
}
if(localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark-mode');

document.querySelectorAll('.reactivate-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const rollNo = this.getAttribute('data-roll');

        Swal.fire({
            title: 'Reactivate User?',
            text: `Are you sure you want to restore access for Roll No: ${rollNo}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ff4757', 
            cancelButtonColor: '#333',
            confirmButtonText: 'Yes, Reactivate!',
            background: '#1e1e1e', 
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `reactivate_user.php?roll_no=${rollNo}`;
            }
        });
    });
});

// Deactivate Button Logic
document.querySelectorAll('.deactivate-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const rollNo = this.getAttribute('data-roll');

        Swal.fire({
            title: 'Deactivate User?',
            text: `Are you sure you want to block Roll No: ${rollNo}? They won't be able to login.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#333',
            cancelButtonColor: '#ff4757',
            confirmButtonText: 'Yes, Block User',
            background: '#1e1e1e',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `deactivate_user.php?roll_no=${rollNo}`;
            }
        });
    });
});
</script>

</body>
</html>