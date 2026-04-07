<?php
session_start();
include 'connection.php';
include 'utils.php';
include 'includes/auth_validate.php';

// Fetching filter options
$filterMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'paid'; // Default to "paid" for revenue

// Query to fetch filtered data
$sql = "SELECT o.order_id, o.total, o.payment_status, o.date, 
        c.customer_name, c.customer_phone
        FROM orders o
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        WHERE MONTH(o.date) = ? AND YEAR(o.date) = ? AND o.type = 1";

if ($statusFilter !== 'all') {
    $sql .= " AND LOWER(o.payment_status) = ?";
}

if (!empty($searchKeyword)) {
    $sql .= " AND (c.customer_name LIKE ? OR c.customer_phone LIKE ? OR o.order_id LIKE ?)";
}

$stmt = $con->prepare($sql);

// Bind parameters dynamically
$params = [$filterMonth, $filterYear];
$types = "ii";

if ($statusFilter !== 'all') {
    $params[] = strtolower($statusFilter);
    $types .= "s";
}

if (!empty($searchKeyword)) {
    $searchParam = "%$searchKeyword%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calculate totals
$totalRevenueQuery = "SELECT SUM(total) AS total_revenue FROM orders WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 1 AND LOWER(payment_status) = 'paid'";
$stmtRevenue = $con->prepare($totalRevenueQuery);
$stmtRevenue->bind_param("ii", $filterMonth, $filterYear);
$stmtRevenue->execute();
$totalRevenue = $stmtRevenue->get_result()->fetch_assoc()['total_revenue'] ?? 0;

$totalPendingQuery = "SELECT SUM(total) AS total_pending FROM orders WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 1 AND LOWER(payment_status) = 'pending'";
$stmtPending = $con->prepare($totalPendingQuery);
$stmtPending->bind_param("ii", $filterMonth, $filterYear);
$stmtPending->execute();
$totalPending = $stmtPending->get_result()->fetch_assoc()['total_pending'] ?? 0;

include_once('includes/header.php');
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-chart-line text-primary"></i> Sales Reports</span>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fa fa-print"></i> Print Report
                </button>
            </h1>
        </div>
    </div>

    <?php include 'includes/flash_messages.php'; ?>

    <div class="card-modern animate-fade-in" style="margin-bottom: 24px;">
        <form method="GET" class="form-inline" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end;">
            <div class="form-group" style="flex: 1; min-width: 140px;">
                <label style="font-weight: 700; font-size: 12px; margin-bottom: 8px; display: block; opacity: 0.6;">MONTH</label>
                <select name="month" class="form-control">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $filterMonth) ? 'selected' : '';
                        echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 100px;">
                <label style="font-weight: 700; font-size: 12px; margin-bottom: 8px; display: block; opacity: 0.6;">YEAR</label>
                <select name="year" class="form-control">
                    <?php
                    for ($i = date('Y') - 5; $i <= date('Y'); $i++) {
                        $selected = ($i == $filterYear) ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 140px;">
                <label style="font-weight: 700; font-size: 12px; margin-bottom: 8px; display: block; opacity: 0.6;">PAYMENT STATUS</label>
                <select name="status" class="form-control">
                    <option value="all" <?php echo ($statusFilter == 'all') ? 'selected' : ''; ?>>All Transactions</option>
                    <option value="paid" <?php echo ($statusFilter == 'paid') ? 'selected' : ''; ?>>Paid Only</option>
                    <option value="pending" <?php echo ($statusFilter == 'pending') ? 'selected' : ''; ?>>Pending Only</option>
                </select>
            </div>
            <div class="form-group" style="flex: 2; min-width: 200px;">
                <label style="font-weight: 700; font-size: 12px; margin-bottom: 8px; display: block; opacity: 0.6;">QUICK SEARCH</label>
                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($searchKeyword); ?>" placeholder="Customer name or phone...">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px !important;">
                    <i class="fa fa-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="row animate-fade-in" style="margin-bottom: 32px;">
        <div class="col-lg-4">
            <div class="card-modern" style="border-left: 4px solid hsl(142 71% 45%) !important;">
                <div class="card-title" style="color: hsl(142 71% 45%);">Total Collected</div>
                <div class="card-value" style="font-size: 2rem;">₹<?php echo number_format($totalRevenue, 2); ?></div>
                <i class="fa fa-sack-dollar card-icon" style="color: hsl(142 71% 45%);"></i>
                <div style="margin-top: 12px; font-size: 12px; font-weight: 600; opacity: 0.5;">
                    Confirmed Revenue for <?php echo date('F Y', mktime(0, 0, 0, $filterMonth, 1, $filterYear)); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-modern" style="border-left: 4px solid hsl(38 92% 50%) !important;">
                <div class="card-title" style="color: hsl(38 92% 50%);">Outstanding</div>
                <div class="card-value" style="font-size: 2rem;">₹<?php echo number_format($totalPending, 2); ?></div>
                <i class="fa fa-clock-rotate-left card-icon" style="color: hsl(38 92% 50%);"></i>
                <div style="margin-top: 12px; font-size: 12px; font-weight: 600; opacity: 0.5;">
                    Estimated collections pending
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-modern" style="border-left: 4px solid hsl(var(--primary)) !important;">
                <div class="card-title" style="color: hsl(var(--primary));">Total Transactions</div>
                <div class="card-value" style="font-size: 2rem;"><?php echo $result->num_rows; ?></div>
                <i class="fa fa-receipt card-icon" style="color: hsl(var(--primary));"></i>
                <div style="margin-top: 12px; font-size: 12px; font-weight: 600; opacity: 0.5;">
                    Processed in current period
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive animate-fade-in">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="12%">Order ID</th>
                    <th width="28%">Customer</th>
                    <th width="20%">Date</th>
                    <th width="15%">Status</th>
                    <th width="25%" class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><span style="font-weight: 700; opacity: 0.5;">#<?php echo $row['order_id']; ?></span></td>
                            <td>
                                <div style="font-weight: 700;"><?php echo $row['customer_name']; ?></div>
                                <div style="font-size: 12px; opacity: 0.6;"><?php echo $row['customer_phone']; ?></div>
                            </td>
                            <td>
                                <span style="font-weight: 600; font-size: 13px;"><?php echo date('d M Y', strtotime($row['date'])); ?></span>
                            </td>
                            <td>
                                <?php if (strtolower($row['payment_status']) == 'paid'): ?>
                                    <span class="badge" style="background-color: hsla(142, 71%, 45%, 0.1); color: hsl(142 71% 45%); border: 1px solid hsla(142, 71%, 45%, 0.2); padding: 4px 12px;">Paid</span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: hsla(38, 92%, 50%, 0.1); color: hsl(38 92% 50%); border: 1px solid hsla(38, 92%, 50%, 0.2); padding: 4px 12px;">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <span style="font-weight: 800; font-size: 15px; color: hsl(var(--foreground));">₹<?php echo number_format($row['total'], 2); ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 60px 20px;">
                            <i class="fa fa-folder-open fa-3x" style="opacity: 0.2; margin-bottom: 16px; display: block;"></i>
                            <div style="font-weight: 600; opacity: 0.4;">No records found for the selected period.</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>
