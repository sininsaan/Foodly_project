<?php
session_start();
include('includes/db.php');
include('includes/header.php');

// Auth check 
if(!isset($_SESSION['roll_no'])) { header("Location: index.php"); exit(); }

$student_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
$student_roll = isset($_SESSION['roll_no']) ? $_SESSION['roll_no'] : '0000';
$student_initial = strtoupper(substr($student_name, 0, 1));

// Fetch menu
$category_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';
$query = "SELECT * FROM menu";
if($category_filter !== 'all') {
    $query .= " WHERE category = '$category_filter'";
}
$result = mysqli_query($conn, $query);

// --- RESET LOGIC START ---
$today = date('Y-m-d');

$reset_check = mysqli_query($conn, "SELECT last_reset_date FROM time_slots LIMIT 1");
$date_row = mysqli_fetch_assoc($reset_check);

if ($date_row && $date_row['last_reset_date'] != $today) {
    
    mysqli_query($conn, "UPDATE time_slots SET current_count = 0, last_reset_date = '$today'");
}

// Fetch time slots
$slots_query = "SELECT *, (max_capacity - current_count) AS slots_left 
                FROM time_slots 
                ORDER BY start_time ASC";
$slots_result = mysqli_query($conn, $slots_query);
$slots = [];
if($slots_result) {
    while($s = mysqli_fetch_assoc($slots_result)) $slots[] = $s;
}

if(empty($slots)) {
    $slots = [
        ['id'=>1,'label'=>'10:00 AM - 11:00 AM','current_count'=>5,'max_capacity'=>30,'is_active'=>1],
        ['id'=>2,'label'=>'11:00 AM - 12:00 PM','current_count'=>30,'max_capacity'=>30,'is_active'=>1],
        ['id'=>3,'label'=>'12:00 PM - 1:00 PM','current_count'=>12,'max_capacity'=>30,'is_active'=>1],
        ['id'=>4,'label'=>'1:00 PM - 2:00 PM','current_count'=>0,'max_capacity'=>30,'is_active'=>1],
    ];
}
?>
<?php
date_default_timezone_set('Asia/Kolkata'); 
$current_time = date('H:i'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly | Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .cart-item-remove {
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 16px;
            padding: 4px; border-radius: 4px; transition: var(--transition);
        }
        .cart-item-remove:hover { color: var(--danger); background: #fde8e8; }
        .cart-empty-state {
            text-align: center; padding: 40px 20px;
            color: var(--text-muted); font-size: 14px;
        }
        .cart-empty-state .icon { font-size: 40px; margin-bottom: 10px; }
        .search-wrap { position: relative; margin-bottom: 28px; }
        .search-input {
            width: 100%; padding: 12px 16px 12px 44px;
            border: 1.5px solid var(--border); border-radius: var(--radius-pill);
            font-family: var(--font-body); font-size: 14px;
            background: #fff; outline: none; transition: var(--transition);
            color: var(--text);
        }
        .search-input:focus { border-color: var(--text); }
        .search-icon {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); font-size: 16px; pointer-events: none;
        }
        .timer-modal {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 400;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(4px);
        }
        .timer-modal.open { opacity: 1; pointer-events: all; }
        .timer-box {
            background: #fff; border-radius: 24px;
            padding: 48px 40px; text-align: center;
            max-width: 380px; width: 90%;
            box-shadow: var(--shadow-lg);
        }
        .timer-circle {
            width: 100px; height: 100px;
            border-radius: 50%;
            border: 5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            position: relative;
        }
        .timer-circle svg {
            position: absolute; top: -5px; left: -5px;
            width: 110px; height: 110px;
            transform: rotate(-90deg);
        }
        .timer-circle svg circle {
            fill: none;
            stroke: var(--primary);
            stroke-width: 5;
            stroke-linecap: round;
            transition: stroke-dashoffset 1s linear;
        }
        .timer-num {
            font-family: var(--font-display);
            font-size: 32px; font-weight: 800;
            color: var(--primary); z-index: 1;
        }

        .card-qty-controls {
            display: none;
            align-items: center;
            gap: 8px;
        }
        .card-qty-controls.visible { display: flex; }
        .card-qty-btn {
            width: 28px; height: 28px;
            border-radius: 50%;
            border: 1.5px solid var(--border);
            background: #fff;
            font-size: 16px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: var(--transition);
            font-family: var(--font-body);
            color: var(--text);
        }
        .card-qty-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
        .card-qty-num {
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            min-width: 18px;
            text-align: center;
        }

        .out-of-stock {
    opacity: 0.5;
    pointer-events: none;
}
.out-of-stock .food-card-img,
.out-of-stock .food-card-img-placeholder {
    filter: grayscale(100%);
}
.out-of-stock .btn-add {
    display: none;
}
.out-of-stock::after {
    content: 'Out of Stock';
    position: absolute;
    top: 12px; right: 12px;
    background: #6b7280;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
}

.logo-text span {
    color: var(--primary); 
}

body.dark-mode .search-input {
    background-color: rgba(255, 255, 255, 0.07) !important;
    color: #ffffff !important; 
    border-color: rgba(255, 255, 255, 0.1) !important;
    caret-color: var(--primary) !important; 
}

body.dark-mode .search-input::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
}

body.dark-mode .timer-box, 
body.dark-mode .modal-box {
    background-color: #1a1a1a !important; /* Deep dark background */
    color: #ffffff !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

body.dark-mode .timer-box h2, 
body.dark-mode .timer-box p {
    color: #ffffff !important;
}

body.dark-mode .btn-solid, 
body.dark-mode .btn-primary-full {
    background-color: var(--primary) !important;
    color: #ffffff !important; /* "Confirm Now" ka text white */
}

body.dark-mode .btn-outline {
    background-color: transparent !important;
    color: #ffffff !important; /* Button text visible rahega */
    border: 1.5px solid rgba(255, 255, 255, 0.2) !important;
}

body.dark-mode .btn-outline:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
}

body.dark-mode .modal-heading,
body.dark-mode .token-label,
body.dark-mode .token-hint {
    color: #ffffff !important;
}

body.dark-mode .token-box {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px dashed rgba(255, 255, 255, 0.2) !important;
}

.timeslot-option.disabled {
    cursor: not-allowed;
    background: rgba(255,255,255,0.05) !important;
    border-color: rgba(255,255,255,0.1) !important;
}

    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
       <a href="logout.php" style = "text-decoration:none;"> <div class="logo-icon">🍽️</div></a>
        <a href="logout.php" style="text-decoration:none;"><span class="logo-text">Food<span>ly</span></span></a>
    </div>


    <a class="nav-item active" href="dashboard.php" id="nav-menu">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        Menu
    </a>
    <a class="nav-item" href="history.php" id="nav-history">
        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        My Orders
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

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Welcome <?php echo htmlspecialchars($student_name); ?> !</h1>
            <p class="page-subtitle">What would you like to eat today?</p>
        </div>
        <button class="cart-btn" onclick="toggleCart()" id="cart-btn">
            🛒 Cart
            <span class="cart-badge" id="cart-count" style="display:none">0</span>
        </button>
    </div>

    <!-- CAROUSEL -->
    <div class="carousel-wrap">
        <div class="carousel-track" id="carousel-track">
            <div class="carousel-slide" style="background-image:url('assets/images/vegsamosacomboC.jpg');background-size:cover;background-position:center;">
                <div class="carousel-slide-content">
                    <span class="carousel-badge">TODAY'S SPECIAL</span>
                    <div class="carousel-slide-title">Veg Samosa Combo 🥟</div>
                    <div class="carousel-slide-sub">2 Samosas + Masala Tea — only ₹25</div>
                </div>
            </div>
            <div class="carousel-slide" style="background-image:url('assets/images/dalmakhanithaliC.jpg');background-size:cover;background-position:center;">
                <div class="carousel-slide-content">
                    <span class="carousel-badge">LUNCH OFFER</span>
                    <div class="carousel-slide-title">Dal Makhani Thali 🍛</div>
                    <div class="carousel-slide-sub">Dal + 2 Roti + Salad — ₹80</div>
                </div>
            </div>
            <div class="carousel-slide" style="background-image:url('assets/images/honeychillipotatoC.jpg');background-size:cover;background-position:center;">
                <div class="carousel-slide-content">
                    <span class="carousel-badge">NEW ITEM</span>
                    <div class="carousel-slide-title">Honey Chilly Potato 🍟</div>
                    <div class="carousel-slide-sub">Crispy & delicious — only ₹80</div>
                </div>
            </div>
        </div>
        <div class="carousel-dots" id="carousel-dots"></div>
    </div>

    <!-- Search -->
    <div class="search-wrap">
        <span class="search-icon">🔍</span>
        <input type="text" class="search-input" id="search-input" placeholder="Search for samosa, tea, noodles..." oninput="filterCards()">
    </div>


    <!-- Price Filter -->
    <div class="price-filter-bar">
        <span class="price-filter-label">💰 Price:</span>
        <button class="price-pill active" onclick="filterByPrice('all', this)">All</button>
        <button class="price-pill" onclick="filterByPrice(30, this)">Under ₹30</button>
        <button class="price-pill" onclick="filterByPrice(60, this)">Under ₹60</button>
        <button class="price-pill" onclick="filterByPrice(100, this)">Under ₹100</button>
        <button class="price-pill" onclick="filterByPrice(150, this)">Under ₹150</button>
    </div>

    <!-- Filter Pills -->
    <div class="filter-bar" style="flex-wrap:wrap;">
        <button class="filter-pill <?php echo ($category_filter==='all')?'active':''; ?>" onclick="filterByCategory('all', event)">All</button>
        <button class="filter-pill <?php echo ($category_filter==='Snacks')?'active':''; ?>" onclick="filterByCategory('Snacks', event)">🥨 Snacks</button>
        <button class="filter-pill <?php echo ($category_filter==='Beverages')?'active':''; ?>" onclick="filterByCategory('Beverages', event)">☕ Beverages</button>
        <button class="filter-pill <?php echo ($category_filter==='Meals')?'active':''; ?>" onclick="filterByCategory('Meals', event)">🍛 Meals</button>
        <button class="filter-pill <?php echo ($category_filter==='Parantha')?'active':''; ?>" onclick="filterByCategory('Parantha', event)">🫓 Parantha</button>
        <button class="filter-pill <?php echo ($category_filter==='South Indian')?'active':''; ?>" onclick="filterByCategory('South Indian', event)">🥞 South Indian</button>
        <button class="filter-pill <?php echo ($category_filter==='Naan Combos')?'active':''; ?>" onclick="filterByCategory('Naan Combos', event)">🫓 Naan</button>
        <button class="filter-pill <?php echo ($category_filter==='Rice Combos')?'active':''; ?>" onclick="filterByCategory('Rice Combos', event)">🍚 Rice</button>
        <button class="filter-pill <?php echo ($category_filter==='Chinese')?'active':''; ?>" onclick="filterByCategory('Chinese', event)">🍜 Chinese</button>
        <button class="filter-pill <?php echo ($category_filter==='Pasta')?'active':''; ?>" onclick="filterByCategory('Pasta', event)">🍝 Pasta</button>
        <button class="filter-pill <?php echo ($category_filter==='Sandwich & Roll')?'active':''; ?>" onclick="filterByCategory('Sandwich & Roll', event)">🥪 Sandwich</button>
        <button class="filter-pill <?php echo ($category_filter==='Maggi')?'active':''; ?>" onclick="filterByCategory('Maggi', event)">🍜 Maggi</button>
        <button class="filter-pill <?php echo ($category_filter==='Pattis')?'active':''; ?>" onclick="filterByCategory('Pattis', event)">🍞 Pattis</button>
    
    </div>

    <!-- Food Grid -->
    <div class="food-grid" id="food-grid">
        <?php
        $emoji_map = ['Snacks'=>'🥨','Beverages'=>'☕','Meals'=>'🍛','South Indian'=>'🥞','Chinese'=>'🍜','Pattis'=>'🍞','Naan Combos'=>'🫓','Rice Combos'=>'🍚','Pasta'=>'🍝','Sandwich & Roll'=>'🥪','Maggi'=>'🍜','Parantha'=>'🫓','Protein Salads'=>'🥗'];
        if($result && mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $cat = htmlspecialchars($row['category'] ?? 'Snacks');
                $emoji = $emoji_map[$cat] ?? '🍽️';
                $price = number_format($row['price'], 2);
                $name = htmlspecialchars($row['item_name']);
                $id = (int)($row['item_id'] ?? $row['id'] ?? 0);
                $img = htmlspecialchars($row['image'] ?? '');
        ?>
        <div class="food-card animate-in <?php echo !$row['is_available'] ? 'out-of-stock' : ''; ?>" data-name="<?php echo strtolower($name); ?>" data-category="<?php echo strtolower($cat); ?>" id="card-<?php echo $id; ?>">
            <?php if(!empty($img) && file_exists("assets/images/$img")): ?>
            <img class="food-card-img" src="assets/images/<?php echo $img; ?>"
                 alt="<?php echo $name; ?>"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="food-card-img-placeholder" style="display:none"><?php echo $emoji; ?></div>
            <?php else: ?>
            <div class="food-card-img-placeholder"><?php echo $emoji; ?></div>
            <?php endif; ?>
            <div class="food-card-body">
                <div class="food-card-category"><?php echo $cat; ?></div>
                <div class="food-card-name"><?php echo $name; ?></div>
                <div class="food-card-subtitle">Available in Canteen</div>
                <div class="food-card-footer">
                    <span class="food-price">₹<?php echo $price; ?></span>
                    <div>
                        <button class="btn-add" id="add-btn-<?php echo $id; ?>"
                            onclick="addToCart(<?php echo $id; ?>, '<?php echo addslashes($name); ?>', <?php echo $row['price']; ?>, this)">
                            + Add
                        </button>
                        <div class="card-qty-controls" id="qty-ctrl-<?php echo $id; ?>">
                            <button class="card-qty-btn" onclick="cardQtyChange(<?php echo $id; ?>, -1)">−</button>
                            <span class="card-qty-num" id="card-qty-<?php echo $id; ?>">0</span>
                            <button class="card-qty-btn" onclick="cardQtyChange(<?php echo $id; ?>, 1)">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php }} else { ?>
        <?php
        $demo = [
            ['id'=>1,'name'=>'Veg Samosa','price'=>15,'cat'=>'Snacks','emoji'=>'🥟'],
            ['id'=>2,'name'=>'Masala Tea','price'=>10,'cat'=>'Beverages','emoji'=>'☕'],
            ['id'=>3,'name'=>'Vada Pav','price'=>20,'cat'=>'Snacks','emoji'=>'🍞'],
            ['id'=>4,'name'=>'Dal Makhani Rice','price'=>70,'cat'=>'Meals','emoji'=>'🍛'],
            ['id'=>5,'name'=>'Veg Noodle','price'=>100,'cat'=>'Chinese','emoji'=>'🍜'],
            ['id'=>6,'name'=>'Butter Naan with Chole','price'=>100,'cat'=>'Naan Combos','emoji'=>'🫓'],
        ];
        foreach($demo as $item): ?>
        <div class="food-card animate-in" data-name="<?php echo strtolower($item['name']); ?>" data-category="<?php echo strtolower($item['cat']); ?>" id="card-<?php echo $item['id']; ?>">
            <div class="food-card-img-placeholder"><?php echo $item['emoji']; ?></div>
            <div class="food-card-body">
                <div class="food-card-category"><?php echo $item['cat']; ?></div>
                <div class="food-card-name"><?php echo $item['name']; ?></div>
                <div class="food-card-subtitle">Available in Canteen</div>
                <div class="food-card-footer">
                    <span class="food-price">₹<?php echo number_format($item['price'],2); ?></span>
                    <div>
                        <button class="btn-add" id="add-btn-<?php echo $item['id']; ?>"
                            onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo $item['name']; ?>', <?php echo $item['price']; ?>, this)">
                            + Add
                        </button>
                        <div class="card-qty-controls" id="qty-ctrl-<?php echo $item['id']; ?>">
                            <button class="card-qty-btn" onclick="cardQtyChange(<?php echo $item['id']; ?>, -1)">−</button>
                            <span class="card-qty-num" id="card-qty-<?php echo $item['id']; ?>">0</span>
                            <button class="card-qty-btn" onclick="cardQtyChange(<?php echo $item['id']; ?>, 1)">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; } ?>
    </div>

<div style="font-size: 12px; color: #888885; margin-top: 40px; text-align: center; display: block; width: 100%; font-family: 'Inter', sans-serif; padding-bottom: 20px; clear: both;">
    Foodly v1.0 &nbsp;·&nbsp; College Canteen
</div>
</main>

<div class="cart-overlay" id="cart-overlay" onclick="toggleCart()"></div>

<div class="cart-drawer" id="cart-drawer">
    <div class="cart-header">
        <span class="cart-title">🛒 Your Order</span>
        <button class="cart-close" onclick="toggleCart()">✕</button>
    </div>

    <div class="cart-body">
        <p class="cart-section-label">Selected Items</p>
        <div id="cart-items-list">
            <div class="cart-empty-state">
                <div class="icon">🛒</div>
                <p>Your cart is empty.<br>Add items from the menu!</p>
            </div>
        </div>

        <div id="slots-section" style="margin-top:24px;display:none;">
            <p class="cart-section-label" style="margin-bottom:12px;">Select Pickup Time Slot</p>
            <div class="timeslot-grid" id="timeslot-grid">
                <?php foreach($slots as $i => $slot):
                    $remaining = $slot['slots_left']; 
                    $full = ($remaining <= 0);
                    
                    // --- TIME CHECK LOGIC ---
                    $time_passed = ($current_time > $slot['end_time']); 
                    $is_disabled = ($full || $time_passed);
                    
                    $label = $slot['label'] ?? ($slot['start_time'].' - '.$slot['end_time']);
                ?>
                <div class="timeslot-option 
                    <?php echo $is_disabled ? 'disabled' : ''; ?> 
                    <?php echo (!$is_disabled && $remaining <= 8) ? 'slot-critical' : ''; ?>
                    <?php echo (!$is_disabled && $i === 0) ? 'selected' : ''; ?>"
                    
                    style="<?php echo $time_passed ? 'opacity: 0.4; pointer-events: none; filter: grayscale(1);' : ''; ?>"
                    
                    onclick="<?php echo $is_disabled ? '' : "selectSlot(this, {$slot['id']})"; ?>"
                    data-slot-id="<?php echo $slot['id']; ?>">
                    
                    <div class="timeslot-time">
                        <span>🕐</span>
                        <span><?php echo htmlspecialchars($label); ?></span>
                    </div>

                    <?php if($time_passed): ?>
                        <span class="timeslot-status" style="color:#666;">❌ Slot Closed</span>
                    <?php elseif($full): ?>
                        <span class="timeslot-status status-full">Full (Max <?php echo $slot['max_capacity']; ?>)</span>
                    <?php elseif($remaining <= 8): ?>
                        <span class="timeslot-status status-filling slot-warning">⚡ Hurry! Only <?php echo $remaining; ?> left</span>
                    <?php else: ?>
                        <span class="timeslot-status status-available"><?php echo $remaining; ?> left</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div> 
        </div> 
    </div> 

    <div class="cart-footer">
        <div class="cart-total-row">
            <span class="cart-total-label">Total Amount</span>
            <span class="cart-total-amount">₹<span id="cart-total">0.00</span></span>
        </div>
        <button class="btn-primary-full" onclick="initiateOrder()" id="place-btn" disabled style="opacity:0.5;cursor:not-allowed;">
            Place Pre-Order
        </button>
        
        <div style="margin-top: 15px; text-align: center; font-size: 11px; color: rgba(255,255,255,0.2); font-family: 'Montserrat', sans-serif;">
            Foodly v1.0 • College Canteen
        </div>
    </div>
</div>

<!-- TIMER MODAL -->
<div class="timer-modal" id="timer-modal">
    <div class="timer-box">
        <div class="timer-circle">
            <svg viewBox="0 0 110 110">
                <circle cx="55" cy="55" r="47" stroke-dasharray="295" stroke-dashoffset="0" id="timer-circle-svg"/>
            </svg>
            <span class="timer-num" id="timer-num">15</span>
        </div>
        <h2 style="font-family:var(--font-display);font-size:22px;font-weight:800;margin-bottom:8px;">Confirm your order?</h2>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:24px;">Review your items. Order will auto-confirm in <strong>10 seconds</strong>.</p>
        <div style="display:flex;gap:10px;">
            <button class="btn-outline" onclick="cancelTimer()">Cancel</button>
            <button class="btn-solid" onclick="confirmOrder()">Confirm Now</button>
        </div>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal-overlay" id="success-modal">
    <div class="modal-box" id="receipt-content">
        <div class="modal-icon success">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color:var(--success)"><path d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 class="modal-heading">Order Placed! 🎉</h2>
        <div class="token-box">
            <div class="token-label">Your Token Number</div>
            <div class="token-number" id="token-display">#402</div>
        </div>
        <p class="token-hint">Show this at the counter for pickup.</p>
        <div class="modal-actions">
            <button class="btn-outline" onclick="downloadReceipt()">📄 Download Receipt</button>
            <button class="btn-solid" onclick="closeSuccess()">Close</button>
        </div>
    </div>
</div>

<script>
let cart = {};
let selectedSlotId = <?php echo !empty($slots) ? $slots[0]['id'] : 1; ?>;
let timerInterval = null;
let timerSec = 10;

function updateCardUI(id) {
    id = String(id);
    const addBtn = document.getElementById('add-btn-' + id);
    const qtyCtrl = document.getElementById('qty-ctrl-' + id);
    const qtyNum = document.getElementById('card-qty-' + id);

    if(!addBtn || !qtyCtrl) return;

    if(cart[id] && cart[id].qty > 0) {
        addBtn.style.display = 'none';
        qtyCtrl.classList.add('visible');
        if(qtyNum) qtyNum.textContent = cart[id].qty;
    } else {
        addBtn.style.display = '';
        qtyCtrl.classList.remove('visible');
    }
}

function cardQtyChange(id, delta) {
    id = String(id);
    if(!cart[id]) return;
    cart[id].qty += delta;
    if(cart[id].qty <= 0) {
        delete cart[id];
    }
    updateCardUI(id);
    renderCart();
}

function addToCart(id, name, price, btnEl) {
    id = String(id);
    // Flying animation
    const btnRect = btnEl.getBoundingClientRect();
    const cartBtn = document.getElementById('cart-btn');
    const cartRect = cartBtn.getBoundingClientRect();

    const fly = document.createElement('div');
    fly.style.cssText = `
        position: fixed;
        width: 40px; height: 40px;
        background: var(--primary);
        border-radius: 50%;
        left: ${btnRect.left + btnRect.width/2 - 20}px;
        top: ${btnRect.top + btnRect.height/2 - 20}px;
        z-index: 9999;
        pointer-events: none;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        transition: all 0.65s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    `;

    const card = btnEl.closest('.food-card');
    const img = card ? card.querySelector('.food-card-img') : null;
    if(img && img.style.display !== 'none' && img.src) {
        fly.style.backgroundImage = `url('${img.src}')`;
        fly.style.backgroundSize = 'cover';
        fly.style.backgroundPosition = 'center';
        fly.style.borderRadius = '10px';
    } else {
        const placeholder = card ? card.querySelector('.food-card-img-placeholder') : null;
        fly.textContent = placeholder ? placeholder.textContent.trim() : '🍽️';
    }

    document.body.appendChild(fly);

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            fly.style.left = (cartRect.left + cartRect.width/2 - 6) + 'px';
            fly.style.top = (cartRect.top + cartRect.height/2 - 6) + 'px';
            fly.style.width = '12px';
            fly.style.height = '12px';
            fly.style.fontSize = '0px';
            fly.style.opacity = '0';
        });
    });

    setTimeout(() => fly.remove(), 700);

    // Add to cart
    if(cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { id: id, name, price, qty: 1 };
    }

    updateCardUI(id);
    renderCart();

    cartBtn.style.background = 'var(--primary)';
    setTimeout(() => cartBtn.style.background = '', 400);
}

function removeFromCart(id) {
    id = String(id);
    delete cart[id];
    updateCardUI(id);
    renderCart();
}

function changeQty(id, delta) {
    id = String(id);
    if(!cart[id]) return;
    cart[id].qty += delta;
    if(cart[id].qty <= 0) delete cart[id];
    updateCardUI(id);
    renderCart();
}

function renderCart() {
    const list = document.getElementById('cart-items-list');
    const countEl = document.getElementById('cart-count');
    const totalEl = document.getElementById('cart-total');
    const slotsSection = document.getElementById('slots-section');
    const placeBtn = document.getElementById('place-btn');

    const items = Object.values(cart);
    const totalQty = items.reduce((s, i) => s + i.qty, 0);
    const totalAmt = items.reduce((s, i) => s + i.price * i.qty, 0);

    if(totalQty > 0) {
        countEl.style.display = 'flex';
        countEl.textContent = totalQty;
    } else {
        countEl.style.display = 'none';
    }

    totalEl.textContent = totalAmt.toFixed(2);

    if(totalQty > 0) {
        placeBtn.disabled = false;
        placeBtn.style.opacity = '1';
        placeBtn.style.cursor = 'pointer';
        slotsSection.style.display = 'block';
    } else {
        placeBtn.disabled = true;
        placeBtn.style.opacity = '0.5';
        placeBtn.style.cursor = 'not-allowed';
        slotsSection.style.display = 'none';
    }

    if(items.length === 0) {
        list.innerHTML = `<div class="cart-empty-state"><div class="icon">🛒</div><p>Your cart is empty.<br>Add items from the menu!</p></div>`;
        return;
    }

    list.innerHTML = items.map(item => `
        <div class="cart-item">
            <span class="cart-item-name">${item.name}</span>
            <div class="cart-item-qty">
                <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
            </div>
            <span class="cart-item-price">₹${(item.price * item.qty).toFixed(2)}</span>
            <button class="cart-item-remove" onclick="removeFromCart(${item.id})" title="Remove item">🗑️</button>
        </div>
    `).join('');
}

function toggleCart() {
    const overlay = document.getElementById('cart-overlay');
    const drawer = document.getElementById('cart-drawer');
    overlay.classList.toggle('open');
    drawer.classList.toggle('open');
}

function selectSlot(el, id) {
    document.querySelectorAll('.timeslot-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    selectedSlotId = id;
}

function initiateOrder() {
    if(Object.keys(cart).length === 0) return;
    toggleCart();
    openTimer();
}

function openTimer() {
    timerSec = 10;
    const modal = document.getElementById('timer-modal');
    modal.classList.add('open');
    updateTimerDisplay();
    timerInterval = setInterval(() => {
        timerSec--;
        updateTimerDisplay();
        if(timerSec <= 0) {
            clearInterval(timerInterval);
            confirmOrder();
        }
    }, 1000);
}

function updateTimerDisplay() {
    document.getElementById('timer-num').textContent = timerSec;
    const circumference = 295;
    const offset = circumference - (timerSec / 10) * circumference;
    document.getElementById('timer-circle-svg').style.strokeDashoffset = offset;
}

function cancelTimer() {
    clearInterval(timerInterval);
    document.getElementById('timer-modal').classList.remove('open');
}

function confirmOrder() {
    clearInterval(timerInterval);
    document.getElementById('timer-modal').classList.remove('open');

    const items = Object.values(cart);
    const total = items.reduce((s,i) => s + i.price * i.qty, 0);

    fetch('place_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ items, slot_id: selectedSlotId, total })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('token-display').textContent = '#' + (data.success ? data.token : Math.floor(100 + Math.random() * 900));
        openSuccess();
        Object.keys(cart).forEach(cid => { updateCardUI(cid); delete cart[cid]; });
        cart = {};
        renderCart();
    })
    .catch(() => {
        document.getElementById('token-display').textContent = '#' + Math.floor(100 + Math.random() * 900);
        openSuccess();
        Object.keys(cart).forEach(cid => { updateCardUI(cid); delete cart[cid]; });
        cart = {};
        renderCart();
    });
}

function openSuccess() {
    document.getElementById('success-modal').classList.add('open');
}

function closeSuccess() {
    document.getElementById('success-modal').classList.remove('open');
}

function downloadReceipt() {
    const el = document.getElementById('receipt-content');
    html2canvas(el, { scale: 2 }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'Foodly_Receipt.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}

function filterCards() {
    const q = document.getElementById('search-input').value.toLowerCase();
    document.querySelectorAll('.food-card').forEach(card => {
        const name = card.dataset.name || '';
        const cat = card.dataset.category || '';
        card.style.display = (name.includes(q) || cat.includes(q)) ? '' : 'none';
    });
}

function filterByCategory(cat, e) {
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    e.target.classList.add('active');
    document.querySelectorAll('.food-card').forEach(card => {
        card.style.display = (cat === 'all' || card.dataset.category === cat.toLowerCase()) ? '' : 'none';
    });
}

let currentSlide = 0;
const track = document.getElementById('carousel-track');
const slides = track ? track.children : [];
const dotsContainer = document.getElementById('carousel-dots');

function buildCarousel() {
    if(!track || slides.length === 0) return;
    for(let i = 0; i < slides.length; i++) {
        const dot = document.createElement('button');
        dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
        dot.onclick = () => goToSlide(i);
        dotsContainer.appendChild(dot);
    }
    setInterval(() => goToSlide((currentSlide + 1) % slides.length), 4000);
}

function goToSlide(n) {
    currentSlide = n;
    track.style.transform = `translateX(-${n * 100}%)`;
    document.querySelectorAll('.carousel-dot').forEach((d, i) => {
        d.classList.toggle('active', i === n);
    });
}

buildCarousel();
</script>
<?php include('includes/order_bar.php'); ?>

<!-- Dark Mode Toggle -->
<button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
    </svg>
</button>
<script>
// Dark mode
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
}
if(localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark-mode');

// Price filter
function filterByPrice(maxPrice, btn) {
    document.querySelectorAll('.price-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.food-card').forEach(card => {
        if(maxPrice === 'all') { card.style.display = ''; return; }
        const priceEl = card.querySelector('.food-price');
        if(!priceEl) return;
        const price = parseFloat(priceEl.textContent.replace('₹','').replace(',',''));
        card.style.display = price <= maxPrice ? '' : 'none';
    });
}

// Smart Recommendation based on past choices (stored in localStorage)
function trackItemView(itemName) {
    let history = JSON.parse(localStorage.getItem('foodly_history') || '[]');
    history.push(itemName);
    if(history.length > 20) history = history.slice(-20);
    localStorage.setItem('foodly_history', JSON.stringify(history));
}

function showRecommendations() {
    let history = JSON.parse(localStorage.getItem('foodly_history') || '[]');
    if(history.length === 0) return;
    const freq = {};
    history.forEach(item => freq[item] = (freq[item] || 0) + 1);
    const top = Object.entries(freq).sort((a,b) => b[1]-a[1]).slice(0,3).map(e => e[0]);
    
    document.querySelectorAll('.food-card').forEach(card => {
        const name = card.querySelector('.food-card-name')?.textContent;
        if(top.includes(name)) {
            const badge = document.createElement('div');
            badge.style.cssText = 'position:absolute;top:10px;right:10px;background:var(--primary);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;z-index:10;';
            badge.textContent = '⭐ Recommended';
            if(!card.querySelector('.rec-badge')) {
                badge.className = 'rec-badge';
                card.appendChild(badge);
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', showRecommendations);

// Auto-refresh order bar every 30 seconds
setInterval(() => {
    fetch(window.location.href)
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newBar = doc.getElementById('order-bar');
            const oldBar = document.getElementById('order-bar');
            if(newBar && oldBar) oldBar.innerHTML = newBar.innerHTML;
            else if(newBar && !oldBar) document.body.appendChild(newBar);
        });
}, 8000);


</script>
</body>
</html>
