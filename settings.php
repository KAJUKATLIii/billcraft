<?php
session_start();
include 'connection.php';
include 'utils.php';
include 'includes/auth_validate.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_admin'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                throw new Exception("Passwords do not match!");
            }
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE admin SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $hashed_password, $_SESSION['user_id']);
        } else {
            $sql = "UPDATE admin SET username = ?, email = ? WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssi", $username, $email, $_SESSION['user_id']);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
        } else {
            throw new Exception("Error updating profile.");
        }
    } catch (Exception $e) {
        $_SESSION['failure'] = $e->getMessage();
    }
    
    redirect("settings.php");
    exit();
}

// Fetch current user data
$user_id = $_SESSION['user_id'];
$admin_query = $con->query("SELECT * FROM admin WHERE id = $user_id");
$admin = $admin_query->fetch_assoc();

include_once('includes/header.php');
?>

<div id="page-wrapper">
    <div class="row animate-fade-in">
        <div class="col-lg-12">
            <h1 class="page-header">
                <span><i class="fa fa-gears text-primary"></i> System Settings</span>
            </h1>
        </div>
    </div>

    <?php include 'includes/flash_messages.php'; ?>

    <div class="row animate-fade-in">
        <!-- Admin Profile Settings -->
        <div class="col-lg-7">
            <div class="card-modern">
                <div style="margin-bottom: 24px;">
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Admin Profile</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Manage your authentication credentials and account details.</p>
                </div>
                
                <form method="POST" action="settings.php">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Username</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-user-circle" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="text" name="username" class="form-control" style="padding-left: 44px !important;" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label style="font-weight: 700; font-size: 13px; margin-bottom: 8px; display: block; opacity: 0.8;">Email Address</label>
                                <div class="input-wrapper" style="position: relative;">
                                    <i class="fa fa-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); opacity: 0.4;"></i>
                                    <input type="email" name="email" class="form-control" style="padding-left: 44px !important;" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin: 32px 0; padding: 24px; background: hsla(var(--primary), 0.03); border-radius: 12px; border: 1px solid hsla(var(--primary), 0.1);">
                        <h5 style="margin-top: 0; font-weight: 700; margin-bottom: 16px;">Security Update</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: 700; font-size: 12px; margin-bottom: 6px; display: block; opacity: 0.6;">NEW PASSWORD</label>
                                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: 700; font-size: 12px; margin-bottom: 6px; display: block; opacity: 0.6;">CONFIRM PASSWORD</label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" name="update_admin" class="btn btn-primary" style="padding: 12px 40px !important;">
                            <i class="fa fa-shield-halved" style="margin-right: 8px;"></i> Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Interface Settings -->
        <div class="col-lg-5">
            <div class="card-modern" style="height: calc(100% - 24px);">
                <div style="margin-bottom: 24px;">
                    <h3 style="margin-top: 0; font-weight: 800; letter-spacing: -0.02em;">Preferences</h3>
                    <p style="opacity: 0.6; font-size: 14px;">Customize your application experience.</p>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: hsla(var(--border), 0.2); border-radius: 12px; margin-bottom: 24px;">
                    <div>
                        <h5 style="margin: 0; font-weight: 700;">Theme Appearance</h5>
                        <p style="margin: 4px 0 0 0; font-size: 12px; opacity: 0.6;">Toggle between light and dark mode.</p>
                    </div>
                    <button onclick="document.getElementById('theme-toggle').click()" class="btn btn-secondary btn-sm" style="border-radius: 20px; padding: 6px 16px;">
                        <i class="fa fa-circle-half-stroke"></i> Switch
                    </button>
                </div>
                
                <h5 style="font-weight: 700; margin-bottom: 16px; opacity: 0.8; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">System Information</h5>
                <div style="background: hsla(var(--foreground), 0.02); border-radius: 12px; overflow: hidden;">
                    <table class="table" style="margin-bottom: 0;">
                        <tr>
                            <td style="border-top: none; padding: 12px 16px; font-size: 13px; opacity: 0.7;">PHP Version</td>
                            <td style="border-top: none; padding: 12px 16px; text-align: right; font-weight: 700;"><?php echo PHP_VERSION; ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; font-size: 13px; opacity: 0.7;">Environment</td>
                            <td style="padding: 12px 16px; text-align: right; font-weight: 700;">Production</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; font-size: 13px; opacity: 0.7;">Database</td>
                            <td style="padding: 12px 16px; text-align: right; font-weight: 700;">MySQL 8.0+</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; border-bottom: none; font-size: 13px; opacity: 0.7;">Server Engine</td>
                            <td style="padding: 12px 16px; border-bottom: none; text-align: right; font-weight: 700;">Apache/Nginx</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>
