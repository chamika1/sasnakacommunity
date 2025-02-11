<?php
require_once 'config/db_connect.php';
session_start();

// Check if user is logged in and is a coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coordinator') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    
    try {
        // First delete the school image if exists
        $sql = "SELECT school_image FROM events WHERE id = ? AND coordinator_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$event_id, $_SESSION['user_id']]);
        $event = $stmt->fetch();
        
        if ($event['school_image'] && $event['school_image'] != 'default_school.png') {
            $image_path = "uploads/school_images/" . $event['school_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete the event
        $sql = "DELETE FROM events WHERE id = ? AND coordinator_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$event_id, $_SESSION['user_id']]);
        
        header("Location: events.php?msg=Event deleted successfully!");
    } catch(PDOException $e) {
        header("Location: events.php?error=Failed to delete event.");
    }
} else {
    header("Location: events.php");
}
exit(); 