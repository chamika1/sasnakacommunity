<?php
require_once 'config/db_connect.php';
session_start();

// Check if user is logged in and is a coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coordinator') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    
    try {
        // First delete the post image if exists
        $sql = "SELECT post_image FROM forum_posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();
        
        if ($post['post_image']) {
            $image_path = "uploads/forum_images/" . $post['post_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete the post (comments will be deleted automatically due to CASCADE)
        $sql = "DELETE FROM forum_posts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        
        header("Location: forum.php?msg=Post deleted successfully!");
    } catch(PDOException $e) {
        header("Location: forum.php?error=Failed to delete post.");
    }
} else {
    header("Location: forum.php");
}
exit(); 