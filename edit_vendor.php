<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

// Check if the form is being submitted
if (isset($_GET['edit_vendor'])) {
    // Validate the phone number to be exactly 10 digits
    if (strlen($_GET['vendor_phone']) != 10 || !is_numeric($_GET['vendor_phone'])) {
        $_SESSION['failure'] = "Phone number must be exactly 10 digits.";
    } else {
        // Check if the phone number already exists in the database (excluding the current vendor's phone)
        $phone = $_GET['vendor_phone'];
        $checkPhoneQuery = "SELECT * FROM vendor WHERE vendor_phone = ? AND id != ?";
        $stmtCheck = $con->prepare($checkPhoneQuery);
        $stmtCheck->bind_param("si", $phone, $_GET['id']);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $_SESSION['failure'] = "Phone number already exists. Please enter a different number.";
        } else {
            try {
                // Update vendor details in the database
                $sql = "UPDATE vendor SET vendor_name = ?, vendor_phone = ?, product_id = ?, 
                                          vendor_quantity = ?, vendor_price = ? WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("sssssi", $_GET['vendor_name'], $_GET['vendor_phone'], $_GET['product_id'],
                                           $_GET['vendor_quantity'], $_GET['vendor_price'], $_GET['id']);
                $stmt->execute();

                $_SESSION['success'] = "Vendor updated successfully.";
                redirect("vendors.php"); 
                exit;
            } catch (mysqli_sql_exception $err) {
                $_SESSION['failure'] = "An error occurred: " . $err->getMessage();
            }
        }
    }
}

// Fetch current vendor details for editing
$sql = "SELECT * FROM vendor WHERE id = " . (int)$_GET['id'];
$result = $con->query($sql);

include_once('includes/header.php');
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-pen-to-square text-primary"></i> Edit Vendor</span>
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
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Update Information</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Modify the vendor details below. Fields marked with * are required.</p>
                </div>
                
                <form class="form" action="edit_vendor.php" method="GET">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Vendor Name *</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-building" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="text" value="<?php echo htmlspecialchars($row['vendor_name']) ?>" class="form-control" style="padding-left: 44px !important;" placeholder="Vendor Name" name="vendor_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Contact Number *</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-phone" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" value="<?php echo $row['vendor_phone'] ?>" class="form-control" style="padding-left: 44px !important;" placeholder="10-digit number" name="vendor_phone" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Product Category</label>
                        <div class="input-wrapper" style="position: relative;">
                            <i class="fa fa-box" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4; z-index: 10;"></i>
                            <select class="form-control" style="padding-left: 44px !important; appearance: none; -webkit-appearance: none;" name="product_id" required>
                                <?php
                                $sql1 = "SELECT * FROM products";
                                $result1 = $con->query($sql1);
                                while ($row1 = $result1->fetch_assoc()) {
                                ?>
                                <option value="<?php echo $row1['product_id'] ?>" <?php if ($row1['product_id'] == $row['product_id']) echo 'selected'; ?>>
                                    <?php echo $row1["product_category"] . " — " . $row1['product_name']; ?> 
                                </option>
                                <?php } ?>
                            </select>
                            <i class="fa fa-chevron-down" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4; pointer-events: none;"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Quantity</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-layer-group" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" value="<?php echo $row['vendor_quantity'] ?>" class="form-control" style="padding-left: 44px !important;" placeholder="Quantity" name="vendor_quantity" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Price (₹)</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-tag" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" value="<?php echo $row['vendor_price'] ?>" class="form-control" style="padding-left: 44px !important;" placeholder="Price" name="vendor_price" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid hsla(var(--border), 0.5); margin: 32px 0;">

                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" name="edit_vendor" value="edit_vendor" style="padding: 10px 40px !important;">
                            Update Vendor <i class="fa fa-check" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
