<?php
$page_title = "Community Forum";
$extra_css = '<link rel="stylesheet" href="css/forum.css">';
require_once 'includes/header.php';
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['user_type'];
    
    // Handle image upload
    $post_image = null;
    if (!empty($_FILES['post_image']['name'])) {
        $target_dir = "uploads/forum_images/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["post_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = 'post_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $valid_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_extension, $valid_types)) {
            if (function_exists('imagecreatetruecolor')) {
                // GD library is available - resize image
                list($width, $height) = getimagesize($_FILES["post_image"]["tmp_name"]);
                
                // Set default size
                $new_width = 800;
                $new_height = 600;
                
                // Create new image
                $new_image = imagecreatetruecolor($new_width, $new_height);
                
                // Load source image
                switch($file_extension) {
                    case 'jpg':
                    case 'jpeg':
                        $source = imagecreatefromjpeg($_FILES["post_image"]["tmp_name"]);
                        break;
                    case 'png':
                        $source = imagecreatefrompng($_FILES["post_image"]["tmp_name"]);
                        // Preserve transparency
                        imagealphablending($new_image, false);
                        imagesavealpha($new_image, true);
                        break;
                    case 'gif':
                        $source = imagecreatefromgif($_FILES["post_image"]["tmp_name"]);
                        break;
                }
                
                // Resize image
                imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                
                // Save image
                switch($file_extension) {
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($new_image, $target_file, 90);
                        break;
                    case 'png':
                        imagepng($new_image, $target_file, 9);
                        break;
                    case 'gif':
                        imagegif($new_image, $target_file);
                        break;
                }
                
                // Free memory
                imagedestroy($new_image);
                imagedestroy($source);
                
            } else {
                // GD library not available - just move the uploaded file
                move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file);
            }
            
            $post_image = $new_filename;
        }
    }

    $sql = "INSERT INTO forum_posts (title, content, user_id, user_type, post_image) VALUES (?, ?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $user_id, $user_type, $post_image]);
        header("Location: forum.php?msg=Post created successfully!");
        exit();
    } catch(PDOException $e) {
        $error = "Failed to create post. Please try again.";
    }
}

// Fetch forum posts
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
        ORDER BY fp.created_at DESC";
$posts = $pdo->query($sql)->fetchAll();
?>

<div class="container">
    <div class="welcome-card card">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
        <?php if(isset($_GET['msg'])) echo "<div class='alert alert-success'>".$_GET['msg']."</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
    </div>

    <div class="forum-content">
        <!-- New Post Form -->
        <div class="new-post-form card">
            <h3>Create New Post</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Image (optional)</label>
                    <input type="file" name="post_image" class="form-control" accept="image/*">
                </div>
                <button type="submit" name="new_post" class="btn btn-primary">Create Post</button>
            </form>
        </div>

        <!-- Forum Posts -->
        <div class="posts-section">
            <h3>Recent Posts</h3>
            <div class="posts-grid">
                <?php foreach($posts as $post): ?>
                    <div class="post card">
                        <h3><a href="view_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                        <div class="post-meta">
                            Posted by <?php echo htmlspecialchars($post['author_name']); ?>
                            (<?php echo ucfirst($post['user_type']); ?>)
                            on <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                            <?php if($_SESSION['user_type'] == 'coordinator'): ?>
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                            <?php endif; ?>
                        </div>
                        <?php if($post['post_image']): ?>
                            <div class="post-image">
                                <img src="uploads/forum_images/<?php echo htmlspecialchars($post['post_image']); ?>" alt="Post Image">
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                        <a href="view_post.php?id=<?php echo $post['id']; ?>" class="btn">
                            View Discussion
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html> 