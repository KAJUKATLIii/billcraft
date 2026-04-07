<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

  if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
include 'connection.php';
function alert_box($msg)
{
  echo "<script>alert(\"".$msg."\")</script>";

}
function redirect($url){
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $_SESSION['success'] ?? 'Action completed successfully.',
            'redirect' => $url
        ]);
        exit;
    }
    if (!headers_sent()) {
        header("Location: $url");
    } else {
        echo '<script type="text/javascript">window.location = "'.$url.'"</script>';
    }
    exit;
}

function getVendorCount() {
    global $con;
    $sql = "SELECT * FROM vendor";
    $vendors = $con->query($sql);
    return mysqli_num_rows($vendors);
}
function getCustomerCount() {
    global $con;
    $sql2 = "SELECT * FROM customer";
    $customer2 = $con->query($sql2);
    return mysqli_num_rows($customer2);
}
function getProductCount() {
    global $con;
    $sql3 = "SELECT * FROM products";
    $product3 = $con->query($sql3);
    return mysqli_num_rows($product3);
}

function Register(string $username1,string $pasword,string $email){
	global $con;
	$pasword = password_hash($pasword,PASSWORD_DEFAULT);

	try{    
        $sql = "INSERT INTO admin (username, email, password, role) VALUES (?, ?, ?, 'admin')";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sss", $username1, $email, $pasword); 
        $stmt->execute();
        $_SESSION['success'] = "Registered Success";
    }
    catch(mysqli_sql_exception $err){
        alert_box($err->getMessage());
    }
}

function Login(string $username1, string $password1){
    global $con;
    try {    
        $stmt = $con->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username1);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password1, $row['password'])) {
                return $row;
            }
        }
        return false;
    } catch (mysqli_sql_exception $err) {
        alert_box($err->getMessage());
        return false;
    } 
}
?>

<?php
// --- ACTION HANDLERS ---

// Delete Vendor
if(isset($_GET['id']) && isset($_GET['delete_vendor'])){
    $vendor_id = (int)$_GET['id'];
    try {
        // Delete cascading items (orders -> order_products)
        $order_ids_result = $con->query("SELECT order_id FROM orders WHERE vendor_id = $vendor_id");
        if ($order_ids_result) {
            while ($order_row = $order_ids_result->fetch_assoc()) {
                $stmt_op = $con->prepare("DELETE FROM orders_product WHERE order_id = ?");
                $stmt_op->bind_param("i", $order_row['order_id']);
                $stmt_op->execute();
            }
            $stmt_o = $con->prepare("DELETE FROM orders WHERE vendor_id = ?");
            $stmt_o->bind_param("i", $vendor_id);
            $stmt_o->execute();
        }
        
        $stmt_v = $con->prepare("DELETE FROM vendor WHERE id = ?");
        $stmt_v->bind_param("i", $vendor_id);
        $stmt_v->execute();

        $_SESSION['success'] = "Vendor and all associated orders deleted successfully.";
    } catch (Exception $e) {
        $_SESSION['failure'] = "Error deleting vendor: " . $e->getMessage();
    }
    redirect('vendors.php');
}

// Delete Customer
if(isset($_GET['id']) && isset($_GET['delete_customer'])){
    $customer_id = (int)$_GET['id'];
    try {
        // Delete cascading items
        $order_ids_result = $con->query("SELECT order_id FROM orders WHERE customer_id = $customer_id");
        if ($order_ids_result) {
            while ($order_row = $order_ids_result->fetch_assoc()) {
                $stmt_op = $con->prepare("DELETE FROM orders_product WHERE order_id = ?");
                $stmt_op->bind_param("i", $order_row['order_id']);
                $stmt_op->execute();
            }
            $stmt_o = $con->prepare("DELETE FROM orders WHERE customer_id = ?");
            $stmt_o->bind_param("i", $customer_id);
            $stmt_o->execute();
        }
        
        $stmt_c = $con->prepare("DELETE FROM customer WHERE customer_id = ?");
        $stmt_c->bind_param("i", $customer_id);
        $stmt_c->execute();

        $_SESSION['success'] = "Customer and all associated orders deleted successfully.";
    } catch (Exception $e) {
        $_SESSION['failure'] = "Error deleting customer: " . $e->getMessage();
    }
    redirect('customer.php');
}

// Payment Status Toggle
if(isset($_GET['payment_status']) && isset($_GET['order_id'])){
    try {
        $stmt = $con->prepare("UPDATE orders SET payment_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $_GET['payment_status'], $_GET['order_id']);
        $stmt->execute();
        $_SESSION['success'] = "Payment status updated to " . ucfirst($_GET['payment_status']);
    } catch (Exception $e) {
        $_SESSION['failure'] = "Error updating payment status: " . $e->getMessage();
    }
    redirect('orders.php');
}

// Delete Order
if(isset($_GET['id']) && isset($_GET['delete_order'])){
    $order_id = (int)$_GET['id'];
    try {
        $stmt_op = $con->prepare("DELETE FROM orders_product WHERE order_id = ?");
        $stmt_op->bind_param("i", $order_id);
        $stmt_op->execute();

        $stmt_o = $con->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt_o->bind_param("i", $order_id);
        $stmt_o->execute();

        $_SESSION['success'] = "Order deleted successfully.";
    } catch (Exception $e) {
        $_SESSION['failure'] = "Error deleting order: " . $e->getMessage();
    }
    redirect('orders.php');
}

// Delete Product
if(isset($_GET['id']) && isset($_GET['delete_product'])){
    $product_id = (int)$_GET['id'];
    try {
        // 1. Delete associated order items
        $stmt_op = $con->prepare("DELETE FROM orders_product WHERE product_id = ?");
        $stmt_op->bind_param("i", $product_id);
        $stmt_op->execute();
        
        // 2. Clear product reference in vendors
        $stmt_v = $con->prepare("UPDATE vendor SET product_id = 1 WHERE product_id = ?");
        $stmt_v->bind_param("i", $product_id);
        $stmt_v->execute();
        
        // 3. Delete the product
        $stmt_p = $con->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt_p->bind_param("i", $product_id);
        $stmt_p->execute();
        
        $_SESSION['success'] = "Product deleted successfully.";
    } catch (Exception $e) {
        $_SESSION['failure'] = "Error deleting product: " . $e->getMessage();
    }
    redirect('product.php');
}
?>