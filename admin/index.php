<?php
session_start();
if(isset($_SESSION['admin'])) { header("Location: dashboard.php"); exit(); }
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg);">
    <div style="width:100%;max-width:400px;padding:20px;">

        <div style="text-align:center;margin-bottom:36px;">
            <div style="width:56px;height:56px;background:var(--text);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px;">🔐</div>
            <h1 style="font-family:var(--font-display);font-size:28px;font-weight:800;">Admin Login</h1>
            <p style="font-size:14px;color:var(--text-muted);margin-top:6px;">DAV Canteen Management</p>
        </div>

        <?php if($error === 'invalid'): ?>
        <div style="background:#fde8e8;border:1px solid #fca5a5;color:#991b1b;padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;">
            ❌ Invalid credentials. Try again.
        </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="admin-pass" class="form-input" placeholder="Enter password" required style="padding-right:44px;">
                    <button type="button" onclick="togglePass('admin-pass', this)" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-primary-full" style="margin-top:8px;background:var(--text);">
                Login to Admin Panel →
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:13px;">
            <a href="../index.php" style="color:var(--primary);font-weight:600;text-decoration:none;">← Back to Student Login</a>
        </p>
    </div>
</div>

<script>
function togglePass(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if(field.type === 'password') {
        field.type = 'text';
        btn.innerHTML = '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"/></svg>';
    } else {
        field.type = 'password';
        btn.innerHTML = '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    }
}
</script>
</body>
</html>
