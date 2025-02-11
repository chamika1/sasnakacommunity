<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo isset($page_title) ? $page_title . " - SUSN" : "SUSN Community"; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/spinner.css">
    <?php if(isset($extra_css)) echo $extra_css; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/spinner.js" defer></script>
</head>
<body>
    <div class="spinner-overlay">
        <div class="spinner">
            <!-- Pulsing Circles -->
            <div class="pulse-circle"></div>
            <div class="pulse-circle"></div>
            <div class="pulse-circle"></div>
            
            <!-- 3D Cube -->
            <div class="cube">
                <div class="cube-face front"></div>
                <div class="cube-face back"></div>
                <div class="cube-face right"></div>
                <div class="cube-face left"></div>
                <div class="cube-face top"></div>
                <div class="cube-face bottom"></div>
            </div>
            
            <div class="spinner-text">Loading awesome content...</div>
        </div>
    </div>
    <header class="main-header">
        <div class="header-content">
            <div class="logo-section">
                <img src="images/logo.png" alt="SUSN Logo" class="org-logo">
                <h1 class="site-title">SUSN Community</h1>
            </div>
            <nav class="nav-buttons">
                <?php if(isset($_SESSION['user_type'])): ?>
                    <a href="forum.php" class="btn">Forum</a>
                    <a href="events.php" class="btn">Events</a>
                    <a href="chat.php" class="btn">Chat</a>
                    <?php if($_SESSION['user_type'] == 'volunteer'): ?>
                        <a href="seminar_history.php" class="btn">Seminars</a>
                    <?php endif; ?>
                    <a href="profile.php" class="btn">Profile</a>
                    <a href="logout.php" class="btn logout-btn">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
</body>
</html> 