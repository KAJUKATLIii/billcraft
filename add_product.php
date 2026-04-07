<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    try {
        // Prepare SQL query
        $sql = "INSERT INTO products (product_category, product_name, product_price, product_stock) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);

        // Bind parameters
        $stmt->bind_param(
            "ssdi", 
            $_POST['product_category'], 
            $_POST['product_name'], 
            $_POST['product_price'], 
            $_POST['product_stock']
        );

        // Execute query
        if ($stmt->execute()) {
            $_SESSION['success'] = "Product added successfully!";
            redirect("product.php"); // Redirect to product listing
            exit;
        } else {
            throw new Exception("Failed to add product: " . $stmt->error);
        }
    } catch (Exception $err) {
        $_SESSION['failure'] = "Error: " . $err->getMessage();
    } finally {
        if(isset($stmt)) $stmt->close();
        $con->close();
    }
}

include_once('includes/header.php');
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-box-open text-primary"></i> Add Product</span>
                <a href="product.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </h1>
        </div>
    </div>

    <div class="row animate-fade-in">
        <div class="col-lg-8 col-lg-offset-2">
            <div class="card-modern">
                <div style="margin-bottom: 24px;">
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Category & Identity</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Define the product's basic information and initial inventory.</p>
                </div>
                
                <form class="form" action="add_product.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Category</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-tags" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="text" class="form-control" style="padding-left: 44px !important;" placeholder="e.g. Bottled Water" name="product_category" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Product Name</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-box" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="text" class="form-control" style="padding-left: 44px !important;" placeholder="e.g. 500ml Pack" name="product_name" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Standard Price</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-dollar-sign" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" step="0.01" class="form-control" style="padding-left: 44px !important;" placeholder="0.00" name="product_price" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Opening Stock</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-cubes" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" class="form-control" style="padding-left: 44px !important;" placeholder="0" name="product_stock" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid hsla(var(--border), 0.5); margin: 32px 0;">

                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fa fa-rotate-left"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary" name="add_product" value="add_product" style="padding: 10px 32px !important;">
                            Save Product <i class="fa fa-check" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
