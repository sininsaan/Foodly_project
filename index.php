<?php
include('includes/header.php');
session_start();
if(isset($_SESSION['roll_no'])) { 
    header("Location: dashboard.php");
    exit();
}
$error = isset($_GET['error']) ? $_GET['error'] : ''; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly — Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* ── INTRO ANIMATION ── */
        body { overflow: hidden; }
        body.ready { overflow: auto; }

        #intro {
            position: fixed;
            inset: 0;
            background: #111110;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #intro-brand {
            display: flex;
            align-items: center;
            gap: 0;
            position: relative;
        }

        #intro-icon {
            width: 56px;
            height: 56px;
            min-width: 56px;
            min-height: 56px;
            background: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            opacity: 0;
            transform: scale(0.5);
            transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.34,1.56,0.64,1);
        }

        #intro-name {
            font-family: var(--font-display);
            font-size: 52px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -2px;
            overflow: hidden;
            max-width: 0;
            white-space: nowrap;
            opacity: 0;
            transition: max-width 0.6s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease;
            margin-left: 16px;
        }

        #intro-name span { color: var(--primary); }

        #intro.step1 #intro-icon {
            opacity: 1;
            transform: scale(1);
        }

        #intro.step2 #intro-name {
            max-width: 300px;
            opacity: 1;
        }

        #intro.step3 {
            animation: splitLeft 0.9s cubic-bezier(0.65,0,0.35,1) forwards;
        }

        #intro.step3 #intro-brand {
            animation: brandLeft 0.7s cubic-bezier(0.65,0,0.35,1) forwards;
        }

        @keyframes splitLeft {
            0%   { clip-path: inset(0 0 0 0); }
            100% { clip-path: inset(0 50% 0 0); }
        }

        @keyframes brandLeft {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-150%); }
        }

        /* LEFT PANEL */
        .login-left {
            position: sticky !important;
            top: 0 !important;
            height: 100vh !important;
            align-self: flex-start !important;
        }

        .left-brand-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .left-brand-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;
            min-height: 52px;
            background: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }

        .login-brand {
            font-size: 38px !important;
            font-family: var(--font-display) !important;
            font-weight: 800 !important;
            color: #fff !important;
            letter-spacing: -1px !important;
            flex-shrink: 0 !important;
            white-space: nowrap !important;
        }

        /* RIGHT PANEL */
        .login-right {
            opacity: 0;
            transition: opacity 0.5s ease;
            overflow-y: auto;
            align-self: center;
        }
        .login-right.visible { opacity: 1; }

        /* ── FORM STYLES ── */
        .alert-error {
            background: #fde8e8;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .tab-switcher {
            display: flex;
            background: var(--bg);
            border-radius: var(--radius-sm);
            padding: 4px;
            margin-bottom: 28px;
        }
        .tab-btn {
            flex: 1;
            padding: 9px;
            border: none;
            background: none;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-muted);
        }
        .tab-btn.active {
            background: #fff;
            color: var(--text);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .form-panel { display: none; }
        .form-panel.active { display: block; }
    </style>
</head>
<body>

<!-- INTRO ANIMATION OVERLAY -->
<div id="intro">
    <div id="intro-brand">
        <div id="intro-icon">🍽️</div>
        <div id="intro-name">Food<span>ly</span></div>
    </div>
</div>

<div class="login-page" id="login-page">

    <!-- LEFT PANEL -->
    <div class="login-left" style="justify-content:center;">
        <div style="position:relative;z-index:1;text-align:center;">
            <div class="left-brand-wrap">
                <div class="left-brand-icon">🍽️</div>
                <span class="login-brand">Food<span>ly</span></span>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="login-right">
        <div class="login-form-box">
            <div class="auth-box-container">
                <h1 class="login-form-title" id="form-title">Login to Foodly</h1>

            <div class="tab-switcher" style="margin-top:20px;">
                <button class="tab-btn active" onclick="switchTab('login')">Student Login</button>
                <button class="tab-btn" onclick="switchTab('register')">Register</button>
            </div>

            
            <!-- LOGIN FORM -->
            <div class="form-panel active" id="panel-login">
                <?php if(isset($_GET['error'])): ?>
    <?php if($_GET['error'] == 'invalid'): ?>
        <div style="background: rgba(255, 71, 87, 0.1); color: #ff4757; padding: 12px 16px; border-radius: 10px; border: 1px solid rgba(255, 71, 87, 0.2); margin-bottom: 20px; display: flex; align-items: center; font-size: 13px; font-weight: 500;">
            <span style="margin-right: 10px; font-size: 16px;">❌</span> Invalid credentials. Try again.
        </div>
    <?php elseif($_GET['error'] == 'suspended'): ?>
    <?php endif; ?>
<?php endif; ?>
                <form action="login_process.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Roll Number</label>
                        <input type="text" name="roll_no" class="form-input" placeholder="e.g. 8104" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
    <div style="position:relative;">
    <input type="password" name="password" id="login-password" class="form-input" placeholder="Enter your password" required style="padding-right:44px;">
    <button type="button" onclick="togglePass('login-password', this)" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
    </button>
</div>
                    </div>
                    <button type="submit" class="btn-primary-full" style="margin-top:8px;">Login →</button>
                </form>
                <p class="form-link" style="margin-top:16px;">
                    Are you canteen staff? <a href="admin/index.php">Admin Login</a>
                </p>
            </div>

            <!-- REGISTER FORM -->
               
            <div class="form-panel" id="panel-register">
                <form action="register_process.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Roll Number</label>
                        <input type="text" name="roll_no" class="form-input" placeholder="e.g. 8104" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" placeholder="Your full name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">College Email</label>
                        <input type="email" name="email" class="form-input" placeholder="you@davchandigarh.ac.in" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-input" placeholder="10-digit mobile number" required>
                    </div>
                   <div class="form-group">
    <label class="form-label">Create Password</label>
    <div style="position:relative;">
        <input type="password" name="password" id="reg-password" class="form-input" placeholder="Minimum 6 characters" required style="padding-right:44px;">
        <button type="button" onclick="togglePass('reg-password', this)" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
    </div>
</div>
                    <button type="submit" class="btn-primary-full" style="margin-top:8px;">Create Account →</button>
                </form>
                
            </div>

   
        </div>
    </div>
<div style="
    position: fixed;
    bottom: 20px;
    right: 30px;
    font-family: 'Montserrat', sans-serif;
    font-size: 11px;
    color: #999;
    letter-spacing: 0.8px;
    z-index: 999;
    pointer-events: none;
">
    Foodly <span style="color: #ddd; margin: 0 4px;">|</span> v1.0
</div>
</div>
  
<script>
const intro = document.getElementById('intro');
const loginPage = document.getElementById('login-page');

setTimeout(() => intro.classList.add('step1'), 200);
setTimeout(() => intro.classList.add('step2'), 700);
setTimeout(() => intro.classList.add('step3'), 1400);

//FADE IN LOGIN PAGE AFTER INTRO
setTimeout(() => {
    intro.style.display = 'none';
    document.body.classList.add('ready');
    setTimeout(() => {
        document.querySelector('.login-right').classList.add('visible');
    }, 100);
}, 2500);

function switchTab(tab) {
    document.getElementById('form-title').textContent = 
        tab === 'login' ? 'Login to Foodly' : 'Sign up to Foodly';
    document.querySelectorAll('.tab-btn').forEach((b,i) => {
        b.classList.toggle('active', (i===0 && tab==='login') || (i===1 && tab==='register'));
    });
    document.getElementById('panel-login').classList.toggle('active', tab==='login');
    document.getElementById('panel-register').classList.toggle('active', tab==='register');

}


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

if (window.location.search.includes('error=suspended')) {
    Swal.fire({
        title: 'ACCOUNT TERMINATED',
        text: 'Your account has been suspended due to 3 missed orders. Please contact the canteen admin to reactivate.',
        icon: 'error',
        confirmButtonText: 'Understood',
        confirmButtonColor: '#ff4757', 
        background: '#1e1e1e', 
        color: '#ffffff'
    });
}
</script>
</body>
</html>
