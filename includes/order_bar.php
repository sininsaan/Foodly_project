<?php
// Active order bar — included in every student page
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

<?php if($bar_order): ?>
<div id="order-bar" style="
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 500;
    width: calc(100% - var(--sidebar-w) - 80px);
    max-width: 700px;
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
        <!-- Token -->
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

        <!-- Progress -->
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

        <!-- Amount + View -->
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

<style>
@keyframes slideUp {
    from { opacity:0; transform:translateX(-50%) translateY(20px); }
    to   { opacity:1; transform:translateX(-50%) translateY(0); }
}
</style>
<?php endif; ?>
