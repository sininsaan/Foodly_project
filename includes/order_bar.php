<?php
// --- SESSION CHECK ---//
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- PATH FIX ---//
include_once(__DIR__ . '/db.php');

// --- CONDITION ---//
$current_page = basename($_SERVER['PHP_SELF']);
$is_dashboard = ($current_page === 'dashboard.php');

$bar_order = null;
if(isset($_SESSION['roll_no'])) {
    $bar_roll = mysqli_real_escape_string($conn, $_SESSION['roll_no']);
    $bar_q = mysqli_query($conn, "SELECT o.*, ts.label as slot_label 
                                   FROM orders o 
                                   LEFT JOIN time_slots ts ON o.slot_id = ts.id
                                   WHERE o.roll_no = '$bar_roll' 
                                   AND o.status NOT IN ('Completed','Missed')
                                   ORDER BY o.created_at DESC LIMIT 1");
    if($bar_q && mysqli_num_rows($bar_q) > 0) {
        $bar_order = mysqli_fetch_assoc($bar_q);
    }
}
?>

<?php if($bar_order && $is_dashboard): ?>
<div id="order-bar" style="
    position: fixed;
    bottom: 24px;
    left: calc(var(--sidebar-w) + (100% - var(--sidebar-w)) / 2);
    transform: translateX(-50%);
    z-index: 500;
    width: calc(100% - var(--sidebar-w) - 60px);
    max-width: 750px;
    animation: slideUp 0.4s cubic-bezier(0.34,1.56,0.64,1);
">
    <div style="
        background: #1A1A18;
        border-radius: 16px;
        padding: 16px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.08);
    ">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="background:var(--primary);border-radius:10px;padding:8px 14px;font-family:var(--font-display);font-size:16px;font-weight:800;color:#fff;white-space:nowrap;">
                #<?php echo $bar_order['token'] ?? $bar_order['id']; ?>
            </div>
            <div>
                <div style="font-size:12px;color:rgba(255,255,255,0.45);margin-bottom:3px;font-weight:500;">
                    🕐 <?php echo htmlspecialchars($bar_order['slot_label'] ?? '—'); ?>
                </div>
                <div style="font-size:14px;font-weight:700;color:#fff;">
                    <?php
                    $status = $bar_order['status'];
                    $icons = ['Pending'=>'⏳ Order Received','Preparing'=>'👨‍🍳 Being Prepared','Ready'=>'✅ Ready for Pickup!'];
                    echo $icons[$status] ?? $status;
                    ?>
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:0;flex:1;max-width:280px;">
            <?php
            $steps = ['Pending','Preparing','Ready'];
            $current_idx = array_search($status, $steps);
            foreach($steps as $i => $step):
                $done   = $i < $current_idx;
                $active = $i === $current_idx;
            ?>
            <div style="display:flex;align-items:center;flex:1;">
                <div style="
                    width:28px;height:28px;border-radius:50%;
                    background:<?php echo ($done||$active)?'var(--primary)':'rgba(255,255,255,0.1)'; ?>;
                    border:2px solid <?php echo ($done||$active)?'var(--primary)':'rgba(255,255,255,0.15)'; ?>;
                    display:flex;align-items:center;justify-content:center;
                    font-size:11px;color:#fff;font-weight:700;flex-shrink:0;
                    <?php echo $active?'box-shadow:0 0 0 4px rgba(232,67,26,0.25);':''; ?>
                "><?php echo $done ? '✓' : ($i+1); ?></div>
                <?php if($i < count($steps)-1): ?>
                <div style="flex:1;height:2px;background:<?php echo $done?'var(--primary)':'rgba(255,255,255,0.1)'; ?>;"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;">
            <div style="text-align:right;">
                <div style="font-size:11px;color:rgba(255,255,255,0.4);">Total</div>
                <div style="font-family:var(--font-display);font-size:16px;font-weight:800;color:#fff;">₹<?php echo number_format($bar_order['total'],2); ?></div>
            </div>
            <a href="history.php" style="background:rgba(255,255,255,0.1);color:#fff;text-decoration:none;padding:8px 14px;border-radius:8px;font-size:12px;font-weight:600;white-space:nowrap;border:1px solid rgba(255,255,255,0.08);">View →</a>
            <button onclick="document.getElementById('order-bar').style.display='none'" style="background:none;border:none;color:rgba(255,255,255,0.3);cursor:pointer;font-size:18px;padding:4px;line-height:1;">✕</button>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    @keyframes slideUp { from { opacity:0; transform:translateX(-50%) translateY(20px); } to { opacity:1; transform:translateX(-50%) translateY(0); } }
    .notif-toast { position: fixed; top: 24px; right: 24px; z-index: 9999; padding: 16px 20px; border-radius: 14px; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; box-shadow: 0 8px 32px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 12px; animation: toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1); max-width: 320px; cursor: pointer; transition: all 0.3s ease; opacity: 0; transform: translateX(40px); }
    .notif-toast.ready { background: #e8f5ee; color: #065f46; border: 1.5px solid #6ee7b7; }
    .notif-toast.missed { background: #fde8e8; color: #991b1b; border: 1.5px solid #fca5a5; }
    .notif-toast-title { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 15px; }
    .notif-toast-sub { font-size: 12px; opacity: 0.8; margin-top: 2px; }
    @keyframes toastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
</style>

<script>
if (typeof window._isStatusLogicRunning === 'undefined') {
    window._isStatusLogicRunning = true;
    window._lastStatusMap = {};
    window._syncInit = false;

    function showToast(type, title, sub) {
        const existing = document.querySelector('.notif-toast');
        if(existing) existing.remove();

        const t = document.createElement('div');
        t.className = 'notif-toast ' + type;
        t.innerHTML = `<div style="font-size:22px;flex-shrink:0;">${type==='ready'?'🎉':'⚠️'}</div>
                       <div><div class="notif-toast-title">${title}</div><div class="notif-toast-sub">${sub}</div></div>`;
        
        t.onclick = () => t.remove();
        document.body.appendChild(t);
    
        setTimeout(() => { t.style.opacity='1'; t.style.transform='translateX(0)'; }, 100);
        
        setTimeout(() => { 
            t.style.opacity='0'; t.style.transform='translateX(40px)'; 
            setTimeout(() => t.remove(), 300); 
        }, 6000);
    }

    function _masterStatusCheck() {
        fetch('get_order_status.php')
            .then(r => r.json())
            .then(data => {
                if(!data.orders) return;
                data.orders.forEach(o => {
                    const prev = window._lastStatusMap[o.id];
                    
                    if(window._syncInit && prev && prev !== o.status) {
                        if(o.status === 'Ready' || o.status === 'Missed') {
                            setTimeout(() => {
                                if (typeof updateOrderCardUI === 'function') {
                                    updateOrderCardUI(o);
                                }
                                const title = (o.status === 'Ready') ? '✅ Order Ready!' : '❌ Order Missed';
                                const sub = 'Token #' + o.token;
                                showToast(o.status.toLowerCase(), title, sub);
                            }, 8000);
                        }
                    } else if (!window._syncInit) {
                        if (typeof updateOrderCardUI === 'function') updateOrderCardUI(o);
                    }
                    window._lastStatusMap[o.id] = o.status;
                });
                window._syncInit = true;
            }).catch(() => {});
    }

    setInterval(_masterStatusCheck, 8000);
    _masterStatusCheck();
}
</script>
