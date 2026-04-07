<?php
 session_start();
    include('connection.php');
    include('utils.php');
    include 'includes/auth_validate.php';

if(isset($_GET['buy'])){
    
    try{    
        $sql1 = "UPDATE `products` SET `product_stock` = product_stock +" . (int)$_GET['vendor_quantity'] . " WHERE `products`.`product_id` = " . (int)$_GET['product_id'];
        $con->query($sql1);
        
        $sql = "INSERT INTO orders (type, vendor_id, total, payment_status, date) VALUES (0, ?, 0, 'pending', CURRENT_TIMESTAMP)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        
        $_SESSION['success'] = "Purchase recorded and stock updated.";
        redirect("vendors.php");
        exit;
    }
    catch(mysqli_sql_exception $err){
        $_SESSION['failure'] = "Error recording purchase: " . $err->getMessage();
    } 
}
    
    if(!isset($_GET['id'])) {
        header("Location: vendors.php");
        exit;
    }

    $id = (int)$_GET['id'];
    $sql = "SELECT v.*, p.product_name, p.product_category 
            FROM vendor v, products p 
            WHERE v.product_id = p.product_id AND v.id = $id";
    $result = $con->query($sql);
    $vendor = $result->fetch_assoc();
    
include_once('includes/header.php'); 
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-cart-arrow-down text-primary"></i> Confirm Purchase</span>
                <a href="vendors.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Vendors
                </a>
            </h1>
        </div>
    </div>

    <?php include 'includes/flash_messages.php'?>

    <div class="row animate-fade-in">
        <div class="col-lg-6 col-lg-offset-3">
            <div class="card-modern" style="padding: 40px; text-align: center;">
                <div style="background: hsla(var(--primary), 0.1); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto;">
                    <i class="fa fa-truck-loading" style="font-size: 32px; color: hsl(var(--primary));"></i>
                </div>
                
                <h2 style="font-weight: 800; margin-bottom: 8px;">Verify Transaction</h2>
                <p style="opacity: 0.6; margin-bottom: 32px;">Please confirm the details below to complete the inventory buy.</p>

                <div style="background: hsla(var(--foreground), 0.02); border-radius: 16px; padding: 24px; text-align: left; margin-bottom: 32px; border: 1px solid hsl(var(--border));">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                        <span style="opacity: 0.5; font-size: 13px; font-weight: 600;">VENDOR</span>
                        <span style="font-weight: 800;"><?php echo $vendor['vendor_name'] ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                        <span style="opacity: 0.5; font-size: 13px; font-weight: 600;">PRODUCT</span>
                        <span style="font-weight: 700;"><?php echo $vendor['product_name'] ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                        <span style="opacity: 0.5; font-size: 13px; font-weight: 600;">CATEGORY</span>
                        <span class="badge" style="background: hsla(var(--primary), 0.1); color: hsl(var(--primary));"><?php echo $vendor['product_category'] ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px; border-top: 1px dashed hsl(var(--border)); padding-top: 16px;">
                        <span style="opacity: 0.5; font-size: 13px; font-weight: 600;">QUANTITY</span>
                        <span style="font-weight: 800; font-size: 18px;"><?php echo $vendor['vendor_quantity'] ?> Units</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="opacity: 0.5; font-size: 13px; font-weight: 600;">UNIT PRICE</span>
                        <span style="font-weight: 800; font-size: 18px; color: hsl(142 71% 45%);">₹<?php echo number_format($vendor['vendor_price'], 2) ?></span>
                    </div>
                </div>

                <form class="form" action="buy.php" method="GET">
                    <input type="hidden" name="id" value="<?php echo $vendor['id'] ?>">
                    <input type="hidden" name="vendor_quantity" value="<?php echo $vendor['vendor_quantity'] ?>">
                    <input type="hidden" name="product_id" value="<?php echo $vendor['product_id'] ?>">
                    
                    <div style="display: flex; gap: 12px;">
                        <a href="vendors.php" class="btn btn-secondary" style="flex: 1; padding: 14px !important;">Cancel</a>
                        <button type="submit" class="btn btn-primary" name="buy" value="buy" style="flex: 2; padding: 14px !important; background: hsl(142 71% 45%) !important; border: none;">
                            Confirm & Update Stock <i class="fa fa-check-double" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>