<?php
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $whatsapp = $_POST['whatsapp'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO volunteers (name, email, phone, whatsapp, address, password) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $phone, $whatsapp, $address, $password]);
        header("Location: login.php?msg=Registration successful! Please login.");
        exit();
    } catch(PDOException $e) {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Volunteer Registration</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <img src="images/logo.png" alt="SUSN Logo" class="register-logo">
            <h1>SUSN Community</h1>
        </div>

        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
        
        <div class="register-card card">
            <h2>Volunteer Registration</h2>
            <form method="POST" action="" class="register-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>WhatsApp Number</label>
                        <input type="tel" name="whatsapp" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary register-btn">Register as Volunteer</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php" class="link-primary">Login here</a></p>
            </div>
        </div>

        <footer class="register-footer">
            <p>&copy; <?php echo date('Y'); ?> SUSN. All rights reserved.</p>
        </footer>
    </div>
</body>
</html> 