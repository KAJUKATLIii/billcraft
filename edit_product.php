<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    try {
        $sql = "UPDATE products SET product_category = ?, product_name = ?, product_price = ?, product_stock = ? WHERE product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssdii", $_POST['product_category'], $_POST['product_name'], $_POST['product_price'], $_POST['product_stock'], $_POST['id']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Product updated successfully!";
            redirect('product.php');
            exit();
        } else {
            throw new Exception("Update failed: " . $stmt->error);
        }
    } catch (Exception $err) {
        $_SESSION['failure'] = $err->getMessage();
        redirect('product.php');
        exit();
    }
}

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $_SESSION['failure'] = "Product not found!";
        redirect('product.php');
        exit();
    }
} else {
    redirect('product.php');
    exit();
}

include_once('includes/header.php');
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-pen-to-square text-primary"></i> Edit Product</span>
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
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Product Details</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Modify the product specification and stock level.</p>
                </div>
                
                <form class="form" action="edit_product.php" method="POST">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <input type="hidden" name="id" value="<?php echo $row['product_id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Category</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-tags" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="text" value="<?php echo htmlspecialchars($row['product_category']); ?>" class="form-control" style="padding-left: 44px !important;" placeholder="Product Category" name="product_category" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Product Name</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-box" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="text" value="<?php echo htmlspecialchars($row['product_name']); ?>" class="form-control" style="padding-left: 44px !important;" placeholder="Product Name" name="product_name" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Price (₹)</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-tag" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" step="0.01" value="<?php echo $row['product_price']; ?>" class="form-control" style="padding-left: 44px !important;" placeholder="0.00" name="product_price" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Current Stock</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-cubes" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="number" value="<?php echo $row['product_stock']; ?>" class="form-control" style="padding-left: 44px !important;" placeholder="0" name="product_stock" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid hsla(var(--border), 0.5); margin: 32px 0;">

                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" name="edit_product" value="edit" style="padding: 10px 40px !important;">
                            Update Product <i class="fa fa-check" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>