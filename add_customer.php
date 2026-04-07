<?php
session_start();
include('connection.php');
include('utils.php');
include 'includes/auth_validate.php';

$popup_message = ''; // Variable to store the popup message

if (isset($_GET['add_customer'])) {
    $customer_name = trim($_GET['customer_name']);
    $customer_phone = trim($_GET['customer_phone']);

    // Server-side validation for 10-digit phone number
    if (!preg_match('/^\d{10}$/', $customer_phone)) {
        $popup_message = "Invalid phone number. Please enter a valid 10-digit phone number.";
    } else {
        try {
            // Check if the phone number already exists
            $check_sql = "SELECT * FROM customer WHERE customer_phone = ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param("s", $customer_phone);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $popup_message = "Phone number already exists. Please use unique details.";
            } else {
                // Insert the new customer
                $sql = "INSERT INTO customer (customer_name, customer_phone) VALUES (?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ss", $customer_name, $customer_phone);
                $stmt->execute();

                $_SESSION['success'] = "Customer added successfully.";
                redirect('customer.php');
                exit;
            }
        } catch (mysqli_sql_exception $err) {
            $popup_message = "Error: " . mysqli_error($con);
        } finally {
            if (isset($stmt)) $stmt->close();
            if (isset($check_stmt)) $check_stmt->close();
            $con->close();
        }
    }
}

include_once('includes/header.php');
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-user-plus text-primary"></i> Add Customer</span>
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
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Customer Details</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Fill in the information below to register a new customer.</p>
                </div>
                
                <form class="form" action="add_customer.php" method="GET" onsubmit="return validateForm()">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-bottom: 24px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Customer Name</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-user" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input
                                        type="text"
                                        class="form-control"
                                        style="padding-left: 44px !important;"
                                        placeholder="Enter full name"
                                        name="customer_name"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group" style="margin-bottom: 32px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Contact Number</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-phone" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input
                                        type="text"
                                        class="form-control"
                                        style="padding-left: 44px !important;"
                                        placeholder="10-digit mobile number"
                                        name="customer_phone"
                                        pattern="\d{10}"
                                        maxlength="10"
                                        required
                                        title="Please enter a valid 10-digit phone number"
                                    >
                                </div>
                                <small style="display: block; margin-top: 8px; opacity: 0.5; font-size: 12px;">We'll never share this contact with anyone else.</small>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid hsla(var(--border), 0.5); margin: 32px 0;">

                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fa fa-rotate-left"></i> Reset Fields
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            name="add_customer"
                            value="add_customer"
                            style="padding: 10px 32px !important;"
                        >
                            Save Customer <i class="fa fa-check" style="margin-left: 8px;"></i>
                        </button>
                    </div>
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
