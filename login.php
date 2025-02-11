<?php
$page_title = "Login";
require_once 'config/db_connect.php';

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: forum.php");
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    $table = ($user_type == 'volunteer') ? 'volunteers' : 'coordinators';
    
    $sql = "SELECT * FROM $table WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user_type;
        $_SESSION['name'] = $user['name'];
        header("Location: forum.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?> - SUSN</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="images/logo.png" alt="SUSN Logo" class="login-logo">
            <h1>SUSN Community</h1>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="login-card card">
            <h2>Login</h2>
            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Login As</label>
                    <select name="user_type" class="form-control" required>
                        <option value="volunteer">Volunteer</option>
                        <option value="coordinator">Coordinator</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary login-btn">Login</button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? Sign up as:</p>
                <div class="signup-buttons">
                    <a href="volunteer_register.php" class="btn btn-secondary">Volunteer</a>
                    <a href="coordinator_register.php" class="btn btn-secondary">Coordinator</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="login-footer">
        <p>&copy; <?php echo date('Y'); ?> SUSN. All rights reserved.</p>
    </footer>
</body>
</html> 