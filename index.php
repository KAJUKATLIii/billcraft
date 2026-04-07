<?php
session_start();

include 'includes/flash_messages.php';
include_once('includes/header.php');
include 'utils.php';
include 'includes/auth_validate.php';
?>
<style>
    .stat-card {
        position: relative;
        overflow: hidden;
    }
    .stat-card-bg-icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 6rem;
        opacity: 0.06;
        pointer-events: none;
        transition: transform 0.4s ease, opacity 0.4s ease;
    }
    .card-modern:hover .stat-card-bg-icon {
        transform: scale(1.1) rotate(-8deg);
        opacity: 0.1;
    }
    .stat-number {
        font-size: 2.75rem;
        font-weight: 800;
        letter-spacing: -0.05em;
        line-height: 1;
        margin-bottom: 8px;
    }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        opacity: 0.6;
        margin-bottom: 20px;
    }
    .stat-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        padding: 7px 14px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .stat-link:hover {
        transform: translateX(3px);
        text-decoration: none;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    @media (max-width: 992px) { .dashboard-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .dashboard-grid { grid-template-columns: 1fr; } }
</style>

<div id="page-wrapper">
    <?php include 'includes/flash_messages.php'; ?>

    <h1 class="page-header">
        Dashboard
        <small style="font-size: 13px; font-weight: 500; color: var(--text-muted);">
            <?php echo date('l, F j, Y'); ?>
        </small>
    </h1>

    <div class="dashboard-grid animate-fade-in">

        <!-- Vendors -->
        <div class="card-modern stat-card" style="border-top: 3px solid hsl(248, 85%, 65%);">
            <i class="fa fa-truck-field stat-card-bg-icon" style="color: hsl(248, 85%, 65%);"></i>
            <div class="stat-label" style="color: hsl(248, 85%, 65%);">Vendors</div>
            <div class="stat-number" style="color: var(--text);"><?php echo getVendorCount(); ?></div>
            <a href="vendors.php" class="stat-link" style="background: hsla(248, 85%, 65%, 0.1); color: hsl(248, 85%, 65%);">
                View all <i class="fa fa-arrow-right" style="font-size: 11px;"></i>
            </a>
        </div>

        <!-- Customers -->
        <div class="card-modern stat-card" style="border-top: 3px solid hsl(142, 71%, 45%);">
            <i class="fa fa-users stat-card-bg-icon" style="color: hsl(142, 71%, 45%);"></i>
            <div class="stat-label" style="color: hsl(142, 71%, 45%);">Customers</div>
            <div class="stat-number" style="color: var(--text);"><?php echo getCustomerCount(); ?></div>
            <a href="customer.php" class="stat-link" style="background: hsla(142, 71%, 45%, 0.1); color: hsl(142, 71%, 45%);">
                View all <i class="fa fa-arrow-right" style="font-size: 11px;"></i>
            </a>
        </div>

        <!-- Products -->
        <div class="card-modern stat-card" style="border-top: 3px solid hsl(199, 89%, 48%);">
            <i class="fa fa-boxes-stacked stat-card-bg-icon" style="color: hsl(199, 89%, 48%);"></i>
            <div class="stat-label" style="color: hsl(199, 89%, 48%);">Products</div>
            <div class="stat-number" style="color: var(--text);"><?php echo getProductCount(); ?></div>
            <a href="product.php" class="stat-link" style="background: hsla(199, 89%, 48%, 0.1); color: hsl(199, 89%, 48%);">
                View all <i class="fa fa-arrow-right" style="font-size: 11px;"></i>
            </a>
        </div>

        <!-- Reports -->
        <div class="card-modern stat-card" style="border-top: 3px solid hsl(38, 92%, 50%);">
            <i class="fa fa-chart-bar stat-card-bg-icon" style="color: hsl(38, 92%, 50%);"></i>
            <div class="stat-label" style="color: hsl(38, 92%, 50%);">Reports</div>
            <div class="stat-number" style="color: var(--text); font-size: 1.6rem;">Analytics</div>
            <a href="reports.php" class="stat-link" style="background: hsla(38, 92%, 50%, 0.1); color: hsl(38, 92%, 50%);">
                View reports <i class="fa fa-arrow-right" style="font-size: 11px;"></i>
            </a>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="card-modern animate-fade-in" style="animation-delay: 0.1s;">
        <h5 style="font-weight: 700; font-size: 14px; margin-bottom: 16px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em;">Quick Actions</h5>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <a href="add_vendor.php" class="btn btn-primary btn-sm"><i class="fa fa-plus" style="margin-right: 6px;"></i>Add Vendor</a>
            <a href="add_customer.php" class="btn btn-success btn-sm"><i class="fa fa-plus" style="margin-right: 6px;"></i>Add Customer</a>
            <a href="add_product.php" class="btn btn-info btn-sm"><i class="fa fa-plus" style="margin-right: 6px;"></i>Add Product</a>
            <a href="sell.php" class="btn btn-warning btn-sm"><i class="fa fa-cart-plus" style="margin-right: 6px;"></i>New Sale</a>
            <a href="orders.php" class="btn btn-sm" style="background: var(--bg2); color: var(--text); border: 1px solid var(--border) !important;"><i class="fa fa-receipt" style="margin-right: 6px;"></i>View Orders</a>
        </div>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>
