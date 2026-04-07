<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

if(isset($_GET['add_vendor'])){
    try{    
        $sql = "INSERT INTO vendor (vendor_name, vendor_phone, product_id, vendor_quantity, vendor_price) VALUES (?,?,?,?,?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssss", $_GET['vendor_name'], $_GET['vendor_phone'],$_GET['product_id'],
                                   $_GET['vendor_quantity'],$_GET['vendor_price']);
        $stmt->execute();
        $_SESSION['success'] = "Vendor added successfully.";
        redirect("vendors.php");
        exit;
    }
    catch(mysqli_sql_exception $err){
        if(mysqli_errno($con)===1062){
            $_SESSION['failure'] = "Phone number already exists. Please use unique details.";
        } else {
            $_SESSION['failure'] = "Error: " . $err->getMessage();
        }
    } finally{
        if(isset($stmt)) $stmt->close();
    }
}
include_once('includes/header.php'); 
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-truck-ramp-box text-primary"></i> Add Vendor</span>
                <a href="vendors.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </h1>
        </div>
    </div>

    <div class="row animate-fade-in">
        <div class="col-lg-8 col-lg-offset-2">
            <div class="card-modern">
                <div style="margin-bottom: 24px;">
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Vendor Details</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Register a new vendor and link them to an existing product.</p>
                </div>
                
                <form class="form" action="add_vendor.php" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Vendor Name</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-building" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="text" class="form-control" style="padding-left: 44px !important;" placeholder="Business Name" name="vendor_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Contact Number</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-phone" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" class="form-control" style="padding-left: 44px !important;" placeholder="Phone" name="vendor_phone" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Link Product</label>
                        <div class="input-wrapper" style="position: relative;">
                            <i class="fa fa-box" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4; z-index: 10;"></i>
                            <select class="form-control" style="padding-left: 44px !important; appearance: none; -webkit-appearance: none;" name="product_id" required>
                                <option value="" disabled selected>Select a product...</option>
                                <?php 
                                $sql = "SELECT * from products ";
                                $result = $con->query($sql);
                                while($row = $result->fetch_assoc()) {
                                ?>
                                <option value="<?php echo $row['product_id'] ?>">
                                    <?php echo $row["product_category"]." — ".$row['product_name'] ?>
                                </option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-chevron-down" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4; pointer-events: none;"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Initial Quantity</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-layer-group" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" class="form-control" style="padding-left: 44px !important;" placeholder="0" name="vendor_quantity" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Unit Price</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-tag" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" step="0.01" class="form-control" style="padding-left: 44px !important;" placeholder="0.00" name="vendor_price" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid hsla(var(--border), 0.5); margin: 32px 0;">

                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fa fa-rotate-left"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary" name="add_vendor" value="add_vendor" style="padding: 10px 32px !important;">
                            Save Vendor <i class="fa fa-check" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>