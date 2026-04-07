<?php
session_start();
include("connection.php");

if(!isset($_GET["order_id"])){
    header("Location: orders.php");
    exit;
}

$order_id = (int)$_GET["order_id"];

$result = $con->query("SELECT
    orders.order_id,
    customer.customer_name,
    customer.customer_phone,
    customer.customer_id,
    products.product_name,
    products.product_category,
    orders_product.quantity,
    products.product_price,
    orders.date,
    orders.payment_status,
    orders.total
FROM
    `orders_product`,
    customer,
    products,
    orders
WHERE
    orders_product.order_id = orders.order_id AND 
    products.product_id = orders_product.product_id AND
    orders.customer_id = customer.customer_id AND
    orders_product.order_id = $order_id");

if($result->num_rows == 0){
    echo "<div style='font-family:sans-serif;text-align:center;padding:40px'>Order #$order_id not found. <a href='orders.php'>Back to Orders</a></div>";
    exit;
}

$rows = $result->fetch_assoc();
$customer_name  = $rows['customer_name'];
$customer_phone = $rows['customer_phone'];
$payment_status = $rows['payment_status'];
$order_date     = $rows['date'];
$order_total    = $rows['total'];
mysqli_data_seek($result, 0);

// Build logo data URI for print compat
$logo_path = __DIR__ . '/logo.png';
$logo_b64 = '';
if (file_exists($logo_path)) {
    $logo_b64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order_id; ?> — BILLCRAFT</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:  hsl(248, 85%, 65%);
            --accent:   hsl(280, 80%, 68%);
            --success:  hsl(142, 71%, 45%);
            --warning:  hsl(38, 92%, 50%);
            --ink:      hsl(224, 50%, 8%);
            --ink-soft: hsl(220, 14%, 46%);
            --paper:    hsl(220, 30%, 97%);
            --white:    #ffffff;
            --border:   hsl(220, 13%, 90%);
            --radius:   14px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
            padding: 32px 20px 64px;
            line-height: 1.6;
        }

        /* ─── Toolbar ─────────────────── */
        .toolbar {
            max-width: 880px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            color: var(--ink-soft);
        }

        .tbtn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid var(--border);
            background: white;
            color: var(--ink);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .tbtn:hover { background: var(--paper); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .tbtn-primary { background: var(--primary); color: white; border-color: transparent; box-shadow: 0 4px 16px hsla(248, 85%, 65%, 0.35); }
        .tbtn-primary:hover { filter: brightness(1.08); }
        .tbtn-wa { background: #25D366; color: white; border-color: transparent; }
        .tbtn-wa:hover { background: #1da851; }

        /* ─── Invoice card ─────────────── */
        .invoice-card {
            max-width: 880px;
            margin: 0 auto;
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: 0 8px 32px rgba(0,0,0,0.07), 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        /* ─── Header ──────────────────── */
        .inv-header {
            background: linear-gradient(135deg, hsl(224, 50%, 8%) 0%, hsl(235, 45%, 14%) 100%);
            padding: 44px 48px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative blobs in header */
        .inv-header::before {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, hsla(248, 85%, 65%, 0.15), transparent 70%);
            top: -120px; right: -80px;
        }
        .inv-header::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, hsla(280, 80%, 68%, 0.1), transparent 70%);
            bottom: -60px; left: 40%;
        }

        .brand-block { position: relative; z-index: 1; }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .brand-logo-box {
            width: 52px;
            height: 52px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px hsla(248, 85%, 65%, 0.4);
            flex-shrink: 0;
        }
        .brand-logo-box img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 26px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.04em;
            line-height: 1;
        }
        .brand-name span { color: hsl(248, 85%, 78%); }

        .brand-tagline {
            font-size: 12px;
            color: hsla(210, 40%, 98%, 0.45);
            font-weight: 500;
            letter-spacing: 0.03em;
            margin-bottom: 16px;
        }

        .brand-contact {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .brand-contact span {
            font-size: 13px;
            color: hsla(210, 40%, 98%, 0.6);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-contact i { color: hsl(248, 85%, 78%); width: 14px; }

        .inv-meta { text-align: right; position: relative; z-index: 1; }
        .inv-meta-label {
            font-size: 11px;
            font-weight: 700;
            color: hsla(210, 40%, 98%, 0.35);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 6px;
        }
        .inv-meta-invoice {
            font-size: 42px;
            font-weight: 800;
            color: hsla(210, 40%, 98%, 0.12);
            letter-spacing: -0.04em;
            line-height: 1;
        }
        .inv-meta-id {
            font-size: 18px;
            font-weight: 800;
            color: white;
            margin-top: 4px;
        }
        .inv-meta-date {
            font-size: 13px;
            color: hsla(210, 40%, 98%, 0.5);
            margin-top: 6px;
        }

        /* ─── Status bar ──────────────── */
        .status-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 48px;
            background: var(--paper);
            border-bottom: 1px solid var(--border);
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .status-pill.paid    { background: hsla(142, 71%, 45%, 0.1); color: hsl(142, 60%, 35%); }
        .status-pill.pending { background: hsla(38, 92%, 50%, 0.1);  color: hsl(38, 75%, 38%); }
        .status-pill i { font-size: 10px; }
        .status-label { font-size: 12px; color: var(--ink-soft); margin-left: auto; font-weight: 500; }

        /* ─── Body ────────────────────── */
        .inv-body { padding: 40px 48px; }

        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 36px;
            padding-bottom: 36px;
            border-bottom: 1px solid var(--border);
        }

        .info-block-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--ink-soft);
            margin-bottom: 8px;
        }
        .info-block-name {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 4px;
        }
        .info-block-sub {
            font-size: 13px;
            color: var(--ink-soft);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-right { text-align: right; }

        /* ─── Table ───────────────────── */
        .inv-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .inv-table thead tr {
            background: var(--paper);
            border-radius: 8px;
        }

        .inv-table th {
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--ink-soft);
            border-bottom: 2px solid var(--border);
            text-align: left;
        }
        .inv-table th.right { text-align: right; }
        .inv-table th.center { text-align: center; }

        .inv-table td {
            padding: 18px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        .inv-table tbody tr:last-child td { border-bottom: none; }
        .inv-table tbody tr:hover td { background: var(--paper); }

        .item-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: hsla(248, 85%, 65%, 0.1);
            color: var(--primary);
            font-size: 11px;
            font-weight: 800;
            margin-right: 8px;
            flex-shrink: 0;
        }
        .item-name { font-weight: 700; color: var(--ink); display: flex; align-items: center; }
        .item-cat  { font-size: 12px; color: var(--ink-soft); margin-top: 2px; padding-left: 34px; }

        .qty-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--paper);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .td-price { font-weight: 600; color: var(--ink-soft); }
        .td-total { font-weight: 800; font-size: 15px; color: var(--ink); }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* ─── Totals ──────────────────── */
        .totals-box {
            display: flex;
            justify-content: flex-end;
        }
        .totals-inner {
            width: 320px;
            background: var(--paper);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            font-size: 14px;
        }
        .totals-row + .totals-row { border-top: 1px solid var(--border); }
        .totals-row .t-label { color: var(--ink-soft); font-weight: 600; }
        .totals-row .t-val   { font-weight: 700; }
        .totals-grand {
            background: linear-gradient(135deg, hsl(224, 50%, 8%), hsl(235, 45%, 14%));
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: none;
        }
        .totals-grand .tg-label { color: hsla(210, 40%, 98%, 0.7); font-size: 13px; font-weight: 600; }
        .totals-grand .tg-value { color: white; font-size: 24px; font-weight: 800; letter-spacing: -0.03em; }

        /* ─── Footer ──────────────────── */
        .inv-footer {
            padding: 28px 48px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .inv-footer-msg { font-size: 13px; color: var(--ink-soft); }
        .inv-footer-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            color: var(--ink);
        }
        .inv-footer-brand .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--primary);
        }

        /* ─── Print ───────────────────── */
        @media print {
            body { background: white; padding: 0; }
            .toolbar, .no-print { display: none !important; }
            .invoice-card {
                box-shadow: none;
                border: none;
                max-width: 100%;
                border-radius: 0;
            }
            .inv-header::before, .inv-header::after { display: none; }
        }

        @media (max-width: 600px) {
            .inv-header { padding: 28px 24px; flex-direction: column; }
            .inv-meta { text-align: left; }
            .inv-body { padding: 24px; }
            .info-grid { grid-template-columns: 1fr; }
            .status-bar { padding: 12px 24px; }
            .inv-footer { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<!-- ─── Toolbar ─────────────────────────────── -->
<div class="toolbar no-print">
    <div class="toolbar-left">
        <a href="orders.php" class="tbtn">
            <i class="fa fa-arrow-left"></i> Orders
        </a>
        <div class="bill-badge">
            <i class="fa fa-file-invoice"></i>
            Invoice #<?php echo $order_id; ?>
        </div>
    </div>
    <div style="display:flex; gap: 10px; flex-wrap: wrap;">
        <?php
            $phone_clean = preg_replace('/\D/', '', $customer_phone);
            $wa_msg = urlencode("*BILLCRAFT INVOICE*\n\nHello *$customer_name*,\nYour invoice for Order *#$order_id* is ready.\n\n*Amount:* ₹" . number_format($order_total, 2) . "\n\nThank you for shopping with us!");
            $wa_url = "https://wa.me/91$phone_clean?text=$wa_msg";
        ?>
        <a href="<?php echo $wa_url; ?>" target="_blank" class="tbtn tbtn-wa">
            <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
        <button onclick="window.print()" class="tbtn tbtn-primary">
            <i class="fa fa-print"></i> Print Invoice
        </button>
    </div>
</div>

<!-- ─── Invoice Card ──────────────────────────── -->
<div class="invoice-card" id="invoice">

    <!-- Header -->
    <div class="inv-header">
        <div class="brand-block">
            <div class="brand-logo-wrap">
                <?php if($logo_b64): ?>
                <div class="brand-logo-box">
                    <img src="<?php echo $logo_b64; ?>" alt="Billcraft Logo">
                </div>
                <?php endif; ?>
                <div>
                    <div class="brand-name">BILL<span>CRAFT</span></div>
                </div>
            </div>
            <div class="brand-tagline">Premium Business Management Solutions</div>
            <div class="brand-contact">
                <span><i class="fa fa-location-dot"></i> Tech Park, Innovation City</span>
                <span><i class="fa fa-phone"></i> +91 80000 00000</span>
                <span><i class="fa fa-envelope"></i> billing@billcraft.io</span>
            </div>
        </div>

        <div class="inv-meta">
            <div class="inv-meta-label">Document</div>
            <div class="inv-meta-invoice">INVOICE</div>
            <div class="inv-meta-id">#ORD-<?php echo str_pad($order_id, 4, '0', STR_PAD_LEFT); ?></div>
            <div class="inv-meta-date">
                <i class="fa fa-calendar-days"></i>
                <?php echo date('d F Y', strtotime($order_date)); ?>
            </div>
        </div>
    </div>

    <!-- Status bar -->
    <div class="status-bar">
        <?php
            $is_paid = strtolower($payment_status) === 'paid';
        ?>
        <span class="status-pill <?php echo $is_paid ? 'paid' : 'pending'; ?>">
            <i class="fa <?php echo $is_paid ? 'fa-circle-check' : 'fa-clock'; ?>"></i>
            <?php echo ucfirst($payment_status); ?>
        </span>
        <span class="status-label no-print">Click status badge on orders page to toggle payment status</span>
    </div>

    <!-- Body -->
    <div class="inv-body">

        <!-- Billed to / Invoice details -->
        <div class="info-grid">
            <div class="info-left">
                <div class="info-block-label">Billed To</div>
                <div class="info-block-name"><?php echo htmlspecialchars($customer_name); ?></div>
                <div class="info-block-sub">
                    <i class="fa fa-phone" style="color: var(--primary); font-size: 12px;"></i>
                    +91 <?php echo htmlspecialchars($customer_phone); ?>
                </div>
            </div>
            <div class="info-right">
                <div class="info-block-label">Invoice Details</div>
                <div style="margin-top: 4px;">
                    <div style="font-size: 13px; color: var(--ink-soft); margin-bottom: 4px;">
                        <span style="font-weight: 700; color: var(--ink);">Order ID:</span> #<?php echo $order_id; ?>
                    </div>
                    <div style="font-size: 13px; color: var(--ink-soft); margin-bottom: 4px;">
                        <span style="font-weight: 700; color: var(--ink);">Date:</span> <?php echo date('d M Y', strtotime($order_date)); ?>
                    </div>
                    <div style="font-size: 13px; color: var(--ink-soft);">
                        <span style="font-weight: 700; color: var(--ink);">Currency:</span> INR (₹)
                    </div>
                </div>
            </div>
        </div>

        <!-- Items table -->
        <table class="inv-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Description</th>
                    <th class="center" style="width: 15%;">Unit Price</th>
                    <th class="center" style="width: 15%;">Qty</th>
                    <th class="right" style="width: 25%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                $item_no = 0;
                while ($row = $result->fetch_assoc()) {
                    $item_no++;
                    $line_total = $row['product_price'] * $row['quantity'];
                    $total += $line_total;
                ?>
                <tr>
                    <td>
                        <div class="item-name">
                            <span class="item-num"><?php echo $item_no; ?></span>
                            <?php echo htmlspecialchars($row['product_name']); ?>
                        </div>
                        <div class="item-cat"><?php echo htmlspecialchars($row['product_category']); ?></div>
                    </td>
                    <td class="text-center td-price">₹<?php echo number_format($row['product_price'], 2); ?></td>
                    <td class="text-center">
                        <span class="qty-badge"><?php echo $row['quantity']; ?></span>
                    </td>
                    <td class="text-right td-total">₹<?php echo number_format($line_total, 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-box">
            <div class="totals-inner">
                <div class="totals-row">
                    <span class="t-label">Subtotal</span>
                    <span class="t-val">₹<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="totals-row">
                    <span class="t-label">Tax (0%)</span>
                    <span class="t-val">₹0.00</span>
                </div>
                <div class="totals-grand">
                    <span class="tg-label">Total Amount Due</span>
                    <span class="tg-value">₹<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="inv-footer">
        <div class="inv-footer-msg">
            Thank you for your business! For any queries regarding this invoice, please contact us.<br>
            <span style="font-size: 12px; margin-top: 4px; display: block;">This is a computer-generated invoice and is valid without a signature.</span>
        </div>
        <div class="inv-footer-brand">
            <div class="dot"></div>
            BILLCRAFT
        </div>
    </div>
</div>

</body>
</html>