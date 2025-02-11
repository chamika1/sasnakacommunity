<?php
$page_title = "Forum Chat";
$extra_css = '<link rel="stylesheet" href="css/chat.css">';
require_once 'includes/header.php';
require_once 'config/db_connect.php';

// Add this at the top of the file after session_start()
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        try {
            // First verify that the sender exists in their respective table
            $check_sql = ($user_type == 'volunteer') 
                ? "SELECT id FROM volunteers WHERE id = ?"
                : "SELECT id FROM coordinators WHERE id = ?";
            
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$user_id]);
            
            if ($check_stmt->rowCount() > 0) {
                // User exists, proceed with message insertion
                $sql = "INSERT INTO forum_chat (sender_id, sender_type, message) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([$user_id, $user_type, $message]);
                
                if ($result) {
                    header("Location: chat.php");
                    exit();
                } else {
                    $error_msg = "Failed to send message. Please try again.";
                }
            } else {
                $error_msg = "Invalid user credentials.";
            }
        } catch(PDOException $e) {
            error_log("Chat Error: " . $e->getMessage());
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}

// Fetch recent messages with sender info
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
$messages = array_reverse($messages); // Show oldest messages first
?>

<div class="container">
    <?php if(isset($error_msg)): ?>
        <div class="alert alert-error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="chat-container card">
        <div class="chat-header">
            <h2>Live Chat</h2>
            <div class="online-status">
                <span class="status-dot"></span>
                <span>Online</span>
            </div>
        </div>

        <div id="chat-messages" class="chat-messages">
            <?php foreach($messages as $msg): ?>
                <div class="message <?php echo $msg['sender_id'] == $user_id ? 'my-message' : ''; ?>">
                    <div class="message-header">
                        <img src="<?php 
                            if ($msg['profile_picture'] == 'default_profile.png' || empty($msg['profile_picture'])) {
                                echo 'images/default_profile.png';
                            } else {
                                echo 'uploads/profile_pictures/' . htmlspecialchars($msg['profile_picture']);
                            }
                        ?>" alt="Profile" class="profile-pic">
                        <div class="message-info">
                            <span class="sender-name">
                                <?php echo htmlspecialchars($msg['sender_name']); ?>
                                <small>(<?php echo ucfirst($msg['sender_type']); ?>)</small>
                            </span>
                            <span class="message-time">
                                <?php echo date('M d, g:i a', strtotime($msg['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="" class="message-form">
            <textarea name="message" class="form-control" placeholder="Type your message..." required></textarea>
            <button type="submit" name="send_message" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.5.5 0 0 1-.9.1l-2.79-3.487-3.487 2.79a.5.5 0 0 1-.8-.4v-2.5a.5.5 0 0 1 .1-.9l14.547-5.819a.5.5 0 0 1 .54.11Z"/>
                </svg>
                Send
            </button>
        </form>
    </div>
</div>

<script>
    function scrollToBottom() {
        var chatBox = document.getElementById('chat-messages');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function refreshChat() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_messages.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('chat-messages').innerHTML = xhr.responseText;
                scrollToBottom();
            }
        };
        xhr.send();
    }

    window.onload = function() {
        scrollToBottom();
        setInterval(refreshChat, 5000);
    };
</script>

<?php require_once 'includes/footer.php'; ?> 