<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

$popup_message = ''; // To store validation messages for popups

if (isset($_GET['edit_customer'])) {
    try {
        // Check if the phone number already exists
        $sql_check = "SELECT customer_id FROM customer WHERE customer_phone = ? AND customer_id != ?";
        $stmt_check = $con->prepare($sql_check);
        $stmt_check->bind_param("si", $_GET['customer_phone'], $_GET['id']);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $popup_message = "Phone number already exists. Please use a unique phone number.";
        } else {
            // Update the customer in the database
            $sql = "UPDATE customer SET customer_name = ?, customer_phone = ? WHERE customer_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssi", $_GET['customer_name'], $_GET['customer_phone'], $_GET['id']);
            $stmt->execute();

            $_SESSION['success'] = "Customer updated successfully.";
            redirect("customer.php");
            exit;
        }
    } catch (mysqli_sql_exception $err) {
        $popup_message = "Error: " . $err->getMessage();
    }
}

// Fetch the customer details
$sql = "SELECT * FROM customer WHERE customer_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();

include_once('includes/header.php');
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-user-pen text-primary"></i> Edit Customer</span>
                <a href="customer.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </h1>
        </div>
    </div>

    <div class="row animate-fade-in">
        <div class="col-lg-8 col-lg-offset-2">
            <div class="card-modern">
                <div style="margin-bottom: 24px;">
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Customer Information</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Keep your customer records up to date.</p>
                </div>
                
                <form class="form" action="edit_customer.php" method="GET">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['customer_id']); ?>">
                    
                    <div class="form-group" style="margin-bottom: 24px;">
                        <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Full Name</label>
                        <div class="input-wrapper" style="position: relative;">
                            <i class="fa fa-user" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                            <input
                                type="text"
                                class="form-control"
                                style="padding-left: 44px !important;"
                                placeholder="Customer Name"
                                name="customer_name"
                                value="<?php echo htmlspecialchars($row['customer_name']); ?>"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 32px;">
                        <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Contact Number</label>
                        <div class="input-wrapper" style="position: relative;">
                            <i class="fa fa-phone" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                            <input
                                type="text"
                                class="form-control"
                                style="padding-left: 44px !important;"
                                placeholder="10-digit phone"
                                name="customer_phone"
                                value="<?php echo htmlspecialchars($row['customer_phone']); ?>"
                                maxlength="10"
                                required
                                title="Please enter a valid 10-digit phone number"
                            >
                        </div>
                    </div>

                    <hr style="border-top: 1px solid hsla(var(--border), 0.5); margin: 32px 0;">

                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" name="edit_customer" value="edit_customer" style="padding: 10px 40px !important;">
                            Update Record <i class="fa fa-check" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Display popup if there's a server-side error message
    const popupMessage = "<?php echo addslashes($popup_message); ?>";
    if (popupMessage) {
        alert(popupMessage);
    }
</script>

<?php include 'includes/footer.php'; ?>
