<?php
$page_title = "My Profile";
$extra_css = '<link rel="stylesheet" href="css/profile.css">';
require_once 'includes/header.php';
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$table = ($user_type == 'volunteer') ? 'volunteers' : 'coordinators';

// Fetch user data
$sql = "SELECT * FROM $table WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        // Get form data with default values from current user data
        $name = isset($_POST['name']) ? $_POST['name'] : $user['name'];
        $phone = isset($_POST['phone']) ? $_POST['phone'] : $user['phone'];
        $whatsapp = isset($_POST['whatsapp']) ? $_POST['whatsapp'] : $user['whatsapp'];
        $address = isset($_POST['address']) ? $_POST['address'] : $user['address'];
        
        // Handle profile picture upload
        if (!empty($_FILES['profile_picture']['name'])) {
            $target_dir = "uploads/profile_pictures/";
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
            $new_filename = $user_type . '_' . $user_id . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            $valid_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_extension, $valid_types)) {
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    // Delete old profile picture if it exists and is not the default
                    if ($user['profile_picture'] != 'default_profile.png') {
                        $old_file = $target_dir . $user['profile_picture'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    
                    // Update database with new filename
                    $sql = "UPDATE $table SET profile_picture = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$new_filename, $user_id]);
                } else {
                    $error_msg = "Failed to upload profile picture.";
                }
            } else {
                $error_msg = "Invalid file type. Please upload JPG, PNG or GIF.";
            }
        }
        
        // Update other profile information
        $sql = "UPDATE $table SET name = ?, phone = ?, whatsapp = ?, address = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $phone, $whatsapp, $address, $user_id]);
        
        $success_msg = "Profile updated successfully!";
        
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        $_SESSION['name'] = $user['name'];
    }
    
    // Handle password change
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE $table SET password = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$hashed_password, $user_id]);
                $success_msg = "Password changed successfully!";
            } else {
                $error_msg = "New passwords do not match!";
            }
        } else {
            $error_msg = "Current password is incorrect!";
        }
    }
}
?>

<div class="container">
    <?php if(isset($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if(isset($error_msg)): ?>
        <div class="alert alert-error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="profile-grid">
        <!-- Profile Picture Section -->
        <div class="profile-sidebar card">
            <div class="profile-picture-section">
                <div class="profile-picture-wrapper">
                    <img src="<?php 
                        if ($user['profile_picture'] == 'default_profile.png' || empty($user['profile_picture'])) {
                            echo 'images/default_profile.png';
                        } else {
                            echo 'uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']);
                        }
                    ?>" alt="Profile Picture" class="profile-picture">
                    <form method="POST" action="" enctype="multipart/form-data" class="upload-form">
                        <label for="profile_picture" class="upload-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                            </svg>
                            Change Photo
                        </label>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="hidden-input">
                        <button type="submit" name="update_profile" class="btn btn-primary upload-btn">Update Picture</button>
                    </form>
                </div>
                <div class="user-info">
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p class="user-type"><?php echo ucfirst($user_type); ?></p>
                </div>
            </div>
        </div>

        <div class="profile-content">
            <!-- Profile Information Section -->
            <div class="profile-section card">
                <h3>Profile Information</h3>
                <form method="POST" action="" class="profile-form">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>WhatsApp Number</label>
                            <input type="tel" name="whatsapp" class="form-control" value="<?php echo htmlspecialchars($user['whatsapp']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>

            <!-- Change Password Section -->
            <div class="profile-section card">
                <h3>Change Password</h3>
                <form method="POST" action="" class="password-form">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-secondary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 