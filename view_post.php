<?php
$page_title = "View Post";
$extra_css = '<link rel="stylesheet" href="css/forum.css">';
require_once 'includes/header.php';
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Check if post ID is provided
if (!isset($_GET['id'])) {
    header("Location: forum.php");
    exit();
}

$post_id = $_GET['id'];

// Fetch the post with author information
$sql = "SELECT fp.*, 
        CASE 
            WHEN fp.user_type = 'volunteer' THEN v.name 
            ELSE c.name 
        END as author_name,
        CASE 
            WHEN fp.user_type = 'volunteer' THEN v.profile_picture 
            ELSE c.profile_picture 
        END as author_profile_picture
        FROM forum_posts fp 
        LEFT JOIN volunteers v ON fp.user_id = v.id AND fp.user_type = 'volunteer'
        LEFT JOIN coordinators c ON fp.user_id = c.id AND fp.user_type = 'coordinator'
        WHERE fp.id = ?";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        header("Location: forum.php");
        exit();
    }
} catch(PDOException $e) {
    header("Location: forum.php");
    exit();
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment'])) {
    $content = trim($_POST['comment']);
    if (!empty($content)) {
        $sql = "INSERT INTO forum_comments (post_id, user_id, user_type, content) VALUES (?, ?, ?, ?)";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$post_id, $_SESSION['user_id'], $_SESSION['user_type'], $content]);
            header("Location: view_post.php?id=" . $post_id);
            exit();
        } catch(PDOException $e) {
            $error = "Failed to add comment. Please try again.";
        }
    }
}

// Fetch comments for this post
$sql = "SELECT fc.*, 
        CASE 
            WHEN fc.user_type = 'volunteer' THEN v.name 
            ELSE c.name 
        END as commenter_name,
        CASE 
            WHEN fc.user_type = 'volunteer' THEN v.profile_picture 
            ELSE c.profile_picture 
        END as commenter_profile_picture
        FROM forum_comments fc 
        LEFT JOIN volunteers v ON fc.user_id = v.id AND fc.user_type = 'volunteer'
        LEFT JOIN coordinators c ON fc.user_id = c.id AND fc.user_type = 'coordinator'
        WHERE fc.post_id = ?
        ORDER BY fc.created_at ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();
?>

<div class="container">
    <div class="post-view card">
        <div class="post-header">
            <div class="author-info">
                <div class="author-details">
                    <span class="author-name"><?php echo htmlspecialchars($post['author_name']); ?></span>
                    <span class="post-meta">
                        <?php echo ucfirst($post['user_type']); ?> • 
                        <?php echo date('M d, Y g:i A', strtotime($post['created_at'])); ?>
                    </span>
                </div>
            </div>
            <a href="forum.php" class="btn btn-secondary">Back to Forum</a>
        </div>

        <div class="post-content">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <div class="content-text">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>

        <?php if($post['post_image']): ?>
            <div class="post-image">
                <img src="uploads/forum_images/<?php echo htmlspecialchars($post['post_image']); ?>" alt="Post Image">
            </div>
        <?php endif; ?>

        <!-- Comments Section -->
        <div class="comments-section">
            <h3>Comments</h3>
            
            <!-- Add Comment Form -->
            <form method="POST" action="" class="comment-form">
                <div class="form-group">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Write a comment..." required></textarea>
                </div>
                <button type="submit" name="add_comment" class="btn btn-primary">Add Comment</button>
            </form>

            <!-- Display Comments -->
            <div class="comments-list">
                <?php if(empty($comments)): ?>
                    <p class="no-comments">No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <div class="comment-meta">
                                    <span class="commenter-name"><?php echo htmlspecialchars($comment['commenter_name']); ?></span>
                                    <span class="comment-time">
                                        <?php echo date('M d, Y g:i A', strtotime($comment['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="comment-content">
                                <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 