<?php
session_start();
require_once 'config/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
    fc.*,
    CASE 
        WHEN fc.sender_type = 'volunteer' THEN v.name 
        ELSE c.name 
    END as sender_name,
    CASE 
        WHEN fc.sender_type = 'volunteer' THEN v.profile_picture 
        ELSE c.profile_picture 
    END as profile_picture
FROM forum_chat fc
LEFT JOIN volunteers v ON fc.sender_id = v.id AND fc.sender_type = 'volunteer'
LEFT JOIN coordinators c ON fc.sender_id = c.id AND fc.sender_type = 'coordinator'
ORDER BY fc.created_at DESC
LIMIT 50";

$messages = $pdo->query($sql)->fetchAll();
$messages = array_reverse($messages);

foreach($messages as $msg): ?>
    <div class="message <?php echo $msg['sender_id'] == $user_id ? 'my-message' : ''; ?>">
        <div class="message-header">
            <img src="<?php 
                if ($msg['profile_picture'] == 'default_profile.png' || empty($msg['profile_picture'])) {
                    echo 'images/default_profile.png';
                } else {
                    echo 'uploads/profile_pictures/' . htmlspecialchars($msg['profile_picture']);
                }
            ?>" alt="Profile" class="profile-pic">
            <span class="sender-name">
                <?php echo htmlspecialchars($msg['sender_name']); ?>
                (<?php echo ucfirst($msg['sender_type']); ?>)
            </span>
            <span class="message-time">
                <?php echo date('M d, g:i a', strtotime($msg['created_at'])); ?>
            </span>
        </div>
        <div class="message-content">
            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
        </div>
    </div>
<?php endforeach; ?> 