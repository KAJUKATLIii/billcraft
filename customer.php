<?php
session_start();

include 'connection.php';
include 'utils.php';
include 'includes/auth_validate.php';

$search = isset($_GET['search']) ? $con->real_escape_string(trim($_GET['search'])) : '';

if ($search) {
    $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM orders WHERE customer_id = c.customer_id AND type=1) AS total_orders,
                (SELECT COALESCE(SUM(total),0) FROM orders WHERE customer_id = c.customer_id AND type=1 AND payment_status='paid') AS total_spent
            FROM customer c
            WHERE c.customer_name LIKE '%$search%' OR c.customer_phone LIKE '%$search%'
            ORDER BY total_orders DESC";
} else {
    $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM orders WHERE customer_id = c.customer_id AND type=1) AS total_orders,
                (SELECT COALESCE(SUM(total),0) FROM orders WHERE customer_id = c.customer_id AND type=1 AND payment_status='paid') AS total_spent
            FROM customer c
            ORDER BY total_orders DESC";
}

$customers = $con->query($sql);
$total_count = $customers ? $customers->num_rows : 0;

include('includes/header.php');
?>

<style>
.customer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 4px;
}

.cust-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 0;
}
.cust-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: hsla(var(--primary-raw), 0.3);
}
.cust-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    opacity: 0; transition: opacity 0.3s ease;
}
.cust-card:hover::after { opacity: 1; }

.cust-avatar {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 20px; font-weight: 800;
    flex-shrink: 0;
}

.cust-header {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px; padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}
.cust-name  { font-weight: 800; font-size: 15px; color: var(--text); }
.cust-phone { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 5px; margin-top: 3px; }

.cust-stats {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 10px; margin-bottom: 16px;
}
.cstat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 3px; }
.cstat-val   { font-size: 15px; font-weight: 800; color: var(--text); }

.cust-actions { display: flex; gap: 8px; margin-top: auto; }
.cust-btn {
    flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 9px 0; border-radius: 10px;
    font-size: 12.5px; font-weight: 700; text-decoration: none;
    border: 1px solid var(--border); background: var(--surface-solid);
    color: var(--text-muted); transition: all 0.2s ease;
}
.cust-btn:hover { border-color: var(--primary); color: var(--primary); text-decoration: none; }
.cust-btn.sell { background: var(--primary); color: white; border-color: transparent; box-shadow: 0 4px 12px hsla(var(--primary-raw), 0.3); }
.cust-btn.sell:hover { filter: brightness(1.1); color: white; }
.cust-btn.del:hover { border-color: hsl(0,84%,60%); color: hsl(0,84%,60%); }

.search-bar {
    display: flex; gap: 12px; align-items: center; flex-wrap: wrap;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 14px; padding: 12px 16px; margin-bottom: 20px;
}
.search-inner { position: relative; flex: 1; min-width: 200px; }
.search-inner i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; pointer-events: none; }
.search-inner input { padding-left: 40px !important; }

.top-customer-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: hsla(38,92%,50%,0.12); color: hsl(38,75%,38%);
    border: 1px solid hsla(38,92%,50%,0.25);
    border-radius: 20px; padding: 2px 8px; font-size: 10.5px; font-weight: 700;
    margin-left: 6px;
}
.dark .top-customer-badge { color: hsl(38,92%,62%); }
</style>

<div id="page-wrapper">
    <?php include 'includes/flash_messages.php'; ?>

    <h1 class="page-header animate-fade-in">
        Customers
        <a href="add_customer.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus" style="margin-right: 6px;"></i> Add Customer
        </a>
    </h1>

    <!-- Search bar -->
    <div class="search-bar animate-fade-in">
        <form method="GET" action="customer.php" style="display:flex; gap:10px; align-items:center; flex:1; flex-wrap:wrap;">
            <div class="search-inner">
                <i class="fa fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search customers by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
            <?php if ($search): ?><a href="customer.php" class="btn btn-sm" style="background: var(--bg2); border: 1px solid var(--border) !important; color: var(--text-muted);">Clear</a><?php endif; ?>
        </form>
        <span style="font-size:12px; color:var(--text-muted); font-weight:600; flex-shrink:0;">
            <?php echo $total_count; ?> customer<?php echo $total_count != 1 ? 's' : ''; ?>
        </span>
    </div>

    <!-- Customer grid -->
    <div class="customer-grid animate-fade-in">
    <?php if ($customers && $customers->num_rows > 0):
        $rank = 0;
        foreach ($customers as $c):
            $rank++;
            $initial = strtoupper(substr($c['customer_name'], 0, 1));
            $is_top = $rank <= 3 && $c['total_orders'] > 0;
    ?>
        <div class="cust-card">
            <div class="cust-header">
                <div class="cust-avatar"><?php echo $initial; ?></div>
                <div>
                    <div class="cust-name">
                        <?php echo htmlspecialchars($c['customer_name']); ?>
                        <?php if($is_top && $c['total_orders'] > 0): ?>
                        <span class="top-customer-badge"><i class="fa fa-star" style="font-size:8px;"></i> Top</span>
                        <?php endif; ?>
                    </div>
                    <div class="cust-phone">
                        <i class="fa fa-phone" style="font-size:10px; color: var(--primary);"></i>
                        <?php echo htmlspecialchars($c['customer_phone']); ?>
                    </div>
                </div>
            </div>

            <div class="cust-stats">
                <div>
                    <div class="cstat-label">Orders</div>
                    <div class="cstat-val"><?php echo $c['total_orders']; ?></div>
                </div>
                <div>
                    <div class="cstat-label">Total Spent</div>
                    <div class="cstat-val" style="color: hsl(142,71%,40%);">₹<?php echo number_format($c['total_spent'], 0); ?></div>
                </div>
            </div>

            <div class="cust-actions">
                <a href="edit_customer.php?id=<?php echo $c['customer_id']; ?>" class="cust-btn" title="Edit">
                    <i class="fa fa-pen-to-square"></i> Edit
                </a>
                <a href="sell.php?id=<?php echo $c['customer_id']; ?>" class="cust-btn sell">
                    <i class="fa fa-circle-dollar-to-slot"></i> Sell
                </a>
                <a href="utils.php?delete_customer=true&id=<?php echo $c['customer_id']; ?>" 
                   class="cust-btn del ajax-delete" 
                   data-confirm="Delete customer <?php echo htmlspecialchars($c['customer_name']); ?>?" 
                   title="Delete">
                    <i class="fa fa-trash-can"></i>
                </a>
            </div>
        </div>
    <?php endforeach;
    else: ?>
        <div style="grid-column:1/-1; text-align:center; padding:64px 24px; color:var(--text-muted);">
            <i class="fa fa-users" style="font-size:3rem; opacity:0.2; display:block; margin-bottom:16px;"></i>
            <p style="font-weight:600; font-size:15px;">No customers found<?php echo $search ? " for \"$search\"" : ''; ?></p>
            <a href="add_customer.php" class="btn btn-primary btn-sm" style="margin-top:16px;"><i class="fa fa-plus"></i> Add First Customer</a>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>