<?php
session_start();
include('includes/db.php');

// Auth check
if(!isset($_SESSION['roll_no'])) { header("Location: index.php"); exit(); }

$student_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
$student_roll = isset($_SESSION['roll_no']) ? $_SESSION['roll_no'] : '0000';
$student_initial = strtoupper(substr($student_name, 0, 1));

// Handle form submit
$success_msg = '';
$error_msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $new_email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $new_phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if(empty($new_name)) {
        $error_msg = 'Name cannot be empty.';
    } elseif(!empty($new_password) && $new_password !== $confirm_password) {
        $error_msg = 'Passwords do not match.';
    } elseif(!empty($new_password) && strlen($new_password) < 6) {
        $error_msg = 'Password must be at least 6 characters.';
    } else {
        // Update query
        if(!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET name='$new_name', email='$new_email', phone='$new_phone', password='$hashed' WHERE roll_no='$student_roll'";
        } else {
            $query = "UPDATE users SET name='$new_name', email='$new_email', phone='$new_phone' WHERE roll_no='$student_roll'";
        }

        $result = mysqli_query($conn, $query);
        if($result) {
            $_SESSION['name'] = $new_name;
            $student_name = $new_name;
            $student_initial = strtoupper(substr($student_name, 0, 1));
            $success_msg = 'Profile updated successfully!';
        } else {
            $error_msg = 'Something went wrong. Try again.';
        }
    }
}

// Fetch current user data
$user_data = [];
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE roll_no='$student_roll'");
if($user_query && mysqli_num_rows($user_query) > 0) {
    $user_data = mysqli_fetch_assoc($user_query);
} else {
    // Demo data
    $user_data = [
        'roll_no' => $student_roll,
        'name' => $student_name,
        'email' => 'student@davchandigarh.ac.in',
        'phone' => '9876543210',
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | My Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 36px;
            max-width: 600px;
        }
        .profile-avatar-wrap {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 32px;
            padding-bottom: 28px;
            border-bottom: 1px solid var(--border);
        }
        .profile-avatar {
            width: 72px; height: 72px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }
        .profile-avatar-name {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
        }
        .profile-avatar-roll {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .alert-success {
            background: #e8f5ee;
            border: 1px solid #6ee7b7;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #fde8e8;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .readonly-field {
            background: var(--bg) !important;
            color: var(--text-muted) !important;
            cursor: not-allowed;
        }
        .password-section {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }
        .section-title {
            font-family: var(--font-display);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--text);
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
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
    <a class="nav-item" href="my_orders.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        My Orders
    </a>
    <a class="nav-item" href="history.php">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Order History
    </a>

    <div class="sidebar-footer">
        <a href="profile.php" style="text-decoration:none;">
            <div class="user-pill" style="cursor:pointer;transition:var(--transition);" onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">
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

<!-- MAIN -->
<main class="main-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">My Profile</h1>
            <p class="page-subtitle">View and update your account details</p>
        </div>
    </div>

    <div class="profile-card">

        <!-- Avatar + Name -->
        <div class="profile-avatar-wrap">
            <div class="profile-avatar"><?php echo $student_initial; ?></div>
            <div>
                <div class="profile-avatar-name"><?php echo htmlspecialchars($user_data['name']); ?></div>
                <div class="profile-avatar-roll">Roll No: <?php echo htmlspecialchars($user_data['roll_no']); ?></div>
            </div>
        </div>

        <?php if($success_msg): ?>
        <div class="alert-success">✅ <?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if($error_msg): ?>
        <div class="alert-error">❌ <?php echo $error_msg; ?></div>
        <?php endif; ?>

        <!-- Edit Form -->
        <form method="POST">

            <div class="form-group">
                <label class="form-label">Roll Number</label>
                <input type="text" class="form-input readonly-field" value="<?php echo htmlspecialchars($user_data['roll_no']); ?>" readonly>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-input" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">College Email</label>
                <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
            </div>

            <!-- Password Section -->
            <div class="password-section">
                <div class="section-title">🔒 Change Password <span style="font-weight:400;color:var(--text-muted);font-family:var(--font-body);font-size:12px;">(leave blank to keep current)</span></div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Min 6 characters">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="Repeat new password">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary-full" style="margin-top:24px;">
                Save Changes
            </button>
        </form>
    </div>
</main>

<?php include('includes/order_bar.php'); ?>
</body>
</html>
