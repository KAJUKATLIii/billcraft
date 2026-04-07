<?php
session_start();

include 'connection.php';
include 'utils.php';
include 'includes/auth_validate.php';

$Server_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/bill.php?order_id=";

// Stats query
$stats = $con->query("SELECT 
    COUNT(*) as total_orders,
    SUM(total) as total_revenue,
    SUM(CASE WHEN payment_status='paid' THEN 1 ELSE 0 END) as paid_count,
    SUM(CASE WHEN payment_status!='paid' THEN 1 ELSE 0 END) as pending_count
FROM orders WHERE type=1")->fetch_assoc();

// Filter params
$search  = isset($_GET['search'])  ? $con->real_escape_string(trim($_GET['search'])) : '';
$status  = isset($_GET['status'])  ? $con->real_escape_string($_GET['status'])  : '';

$where = "orders_product.order_id = orders.order_id 
          AND products.product_id = orders_product.product_id 
          AND orders.customer_id = customer.customer_id 
          AND orders.type = 1";
if ($search) $where .= " AND (orders.order_id LIKE '%$search%' OR customer.customer_name LIKE '%$search%')";
if ($status) $where .= " AND orders.payment_status = '$status'";

$sql = "SELECT orders.order_id, customer.customer_name, customer.customer_phone, 
               orders.payment_status, orders.date, orders.total
        FROM orders_product, customer, products, orders
        WHERE $where
        GROUP BY orders.order_id
        ORDER BY orders.order_id DESC";

$orders = $con->query($sql);

include('includes/header.php');
?>

<style>
.stats-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 768px) { .stats-strip { grid-template-columns: repeat(2,1fr); } }

.sstat {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.sstat:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
.sstat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); }
.sstat-value { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.04em; color: var(--text); }
.sstat-icon {
    position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
    font-size: 2.5rem; opacity: 0.07; pointer-events: none;
}

.filter-bar {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-tabs {
    display: flex;
    gap: 6px;
    background: var(--bg2);
    border-radius: 10px;
    padding: 4px;
}
.filter-tab {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    color: var(--text-muted);
    transition: all 0.2s ease;
    white-space: nowrap;
}
.filter-tab.active, .filter-tab:hover {
    background: var(--surface-solid);
    color: var(--text);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.filter-tab.active { color: var(--primary); }

.search-wrap { flex: 1; min-width: 200px; position: relative; }
.search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; }
.search-wrap input { padding-left: 40px !important; }

.order-status-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    cursor: pointer; text-decoration: none; border: 1px solid transparent;
    transition: all 0.2s ease;
}
.order-status-pill.paid    { background: hsla(142,71%,45%,0.1); color: hsl(142,60%,33%); border-color: hsla(142,71%,45%,0.25); }
.order-status-pill.pending { background: hsla(38,92%,50%,0.1);  color: hsl(38,75%,35%);  border-color: hsla(38,92%,50%,0.25); }
.order-status-pill:hover { filter: brightness(0.9); }

.dark .order-status-pill.paid    { color: hsl(142,71%,58%); }
.dark .order-status-pill.pending { color: hsl(38,92%,62%); }

.action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; height: 34px; border-radius: 8px;
    font-size: 13px; text-decoration: none; transition: all 0.2s ease;
    border: 1px solid var(--border); color: var(--text-muted);
    background: var(--surface-solid);
}
.action-btn:hover { border-color: var(--primary); color: var(--primary); transform: scale(1.05); }
.action-btn.wa    { color: #25D366; border-color: rgba(37,211,102,0.3); }
.action-btn.wa:hover { background: rgba(37,211,102,0.08); }
.action-btn.del   { color: hsl(0,84%,60%); border-color: hsla(0,84%,60%,0.25); }
.action-btn.del:hover { background: hsla(0,84%,60%,0.08); }

.empty-state {
    text-align: center;
    padding: 64px 24px;
    color: var(--text-muted);
}
.empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 16px; display: block; }
.empty-state p { font-weight: 600; font-size: 15px; }
</style>

<div id="page-wrapper">
    <?php include 'includes/flash_messages.php'; ?>

    <h1 class="page-header animate-fade-in">
        Orders
        <a href="sell.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus" style="margin-right:6px;"></i> New Sale
        </a>
    </h1>

    <!-- Stats strip -->
    <div class="stats-strip animate-fade-in">
        <div class="sstat" style="border-top: 3px solid var(--primary);">
            <span class="sstat-label">Total Orders</span>
            <span class="sstat-value"><?php echo $stats['total_orders'] ?? 0; ?></span>
            <i class="fa fa-receipt sstat-icon" style="color: var(--primary);"></i>
        </div>
        <div class="sstat" style="border-top: 3px solid hsl(142,71%,45%);">
            <span class="sstat-label">Revenue</span>
            <span class="sstat-value" style="font-size: 1.4rem;">₹<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></span>
            <i class="fa fa-indian-rupee-sign sstat-icon" style="color: hsl(142,71%,45%);"></i>
        </div>
        <div class="sstat" style="border-top: 3px solid hsl(142,71%,45%);">
            <span class="sstat-label">Paid</span>
            <span class="sstat-value" style="color: hsl(142,71%,45%);"><?php echo $stats['paid_count'] ?? 0; ?></span>
            <i class="fa fa-circle-check sstat-icon" style="color: hsl(142,71%,45%);"></i>
        </div>
        <div class="sstat" style="border-top: 3px solid hsl(38,92%,50%);">
            <span class="sstat-label">Pending</span>
            <span class="sstat-value" style="color: hsl(38,92%,50%);"><?php echo $stats['pending_count'] ?? 0; ?></span>
            <i class="fa fa-clock sstat-icon" style="color: hsl(38,92%,50%);"></i>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar animate-fade-in">
        <div class="filter-tabs">
            <a href="orders.php" class="filter-tab <?php echo !$status ? 'active' : ''; ?>">All</a>
            <a href="orders.php?status=paid<?php echo $search ? '&search='.$search : ''; ?>" class="filter-tab <?php echo $status=='paid' ? 'active' : ''; ?>">Paid</a>
            <a href="orders.php?status=pending<?php echo $search ? '&search='.$search : ''; ?>" class="filter-tab <?php echo $status=='pending' ? 'active' : ''; ?>">Pending</a>
        </div>

        <form method="GET" action="orders.php" style="display:flex; gap:10px; flex: 1; align-items: center;">
            <?php if ($status): ?><input type="hidden" name="status" value="<?php echo $status; ?>"><?php endif; ?>
            <div class="search-wrap">
                <i class="fa fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search orders or customers..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
            <?php if ($search): ?><a href="orders.php<?php echo $status ? '?status='.$status : ''; ?>" class="btn btn-sm" style="background: var(--bg2); border: 1px solid var(--border) !important; color: var(--text-muted);">Clear</a><?php endif; ?>
        </form>
    </div>

    <!-- Orders table -->
    <div class="table-responsive animate-fade-in">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="10%">Order #</th>
                    <th width="25%">Customer</th>
                    <th width="15%">Date</th>
                    <th width="15%">Status</th>
                    <th width="15%">Total</th>
                    <th width="20%" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders && $orders->num_rows > 0):
                    foreach ($orders as $order):
                        $is_paid = strtolower($order['payment_status']) === 'paid';
                        $toggle_status = $is_paid ? 'pending' : 'paid';
                        $phone = preg_replace('/\D/', '', $order['customer_phone']);
                        $wa_msg = urlencode("*BILLCRAFT INVOICE*\n\nHello *{$order['customer_name']}*,\nOrder *#{$order['order_id']}* — *₹" . number_format($order['total'],2) . "*\n\nThank you!");
                        $wa_url = "https://wa.me/91$phone?text=$wa_msg";
                ?>
                <tr>
                    <td>
                        <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">#<?php echo $order['order_id']; ?></span>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--text); font-size: 14px;"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            <i class="fa fa-phone" style="font-size: 10px;"></i> <?php echo $order['customer_phone']; ?>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 13px; font-weight: 600;"><?php echo date('d M Y', strtotime($order['date'])); ?></div>
                        <div style="font-size: 11px; color: var(--text-muted);"><?php echo date('g:i A', strtotime($order['date'])); ?></div>
                    </td>
                    <td>
                        <a href="utils.php?payment_status=<?php echo $toggle_status; ?>&order_id=<?php echo $order['order_id']; ?>" 
                           class="order-status-pill <?php echo $is_paid ? 'paid' : 'pending'; ?>"
                           title="Click to toggle status">
                            <i class="fa <?php echo $is_paid ? 'fa-circle-check' : 'fa-clock'; ?>"></i>
                            <?php echo $is_paid ? 'Paid' : 'Pending'; ?>
                        </a>
                    </td>
                    <td>
                        <span style="font-size: 15px; font-weight: 800; letter-spacing: -0.02em;">₹<?php echo number_format($order['total'], 2); ?></span>
                    </td>
                    <td class="text-right">
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            <a href="<?php echo $wa_url; ?>" target="_blank" class="action-btn wa" title="Send on WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                            <a href="bill.php?order_id=<?php echo $order['order_id']; ?>" class="action-btn" title="View Invoice">
                                <i class="fa fa-file-invoice"></i>
                            </a>
                            <a href="utils.php?delete_order=true&id=<?php echo $order['order_id']; ?>" 
                               class="action-btn del ajax-delete" 
                               data-confirm="Delete order #<?php echo $order['order_id']; ?>? This cannot be undone."
                               title="Delete Order">
                                <i class="fa fa-trash-can"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach;
                else: ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fa fa-receipt"></i>
                            <p>No orders found<?php echo $search ? " for \"$search\"" : ''; ?></p>
                            <?php if ($search || $status): ?>
                            <a href="orders.php" class="btn btn-sm" style="margin-top: 12px; background: var(--bg2); border: 1px solid var(--border) !important; color: var(--text);">Clear filters</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>