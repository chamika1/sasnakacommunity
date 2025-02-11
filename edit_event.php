<?php
$page_title = "Edit Event";
$extra_css = '<link rel="stylesheet" href="css/events.css">';
require_once 'includes/header.php';
require_once 'config/db_connect.php';

// Check if user is logged in and is a coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coordinator') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: events.php");
    exit();
}

$event_id = $_GET['id'];

// Fetch event details
$sql = "SELECT * FROM events WHERE id = ? AND coordinator_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$event_id, $_SESSION['user_id']]);
$event = $stmt->fetch();

if (!$event) {
    header("Location: events.php?error=Event not found.");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $school_name = $_POST['school_name'];
    $location = $_POST['location'];
    $city = $_POST['city'];
    $event_date = $_POST['event_date'];
    $students_count = $_POST['students_count'];
    
    // Handle image upload if new image is provided
    $school_image = $event['school_image'];
    if (!empty($_FILES['school_image']['name'])) {
        $target_dir = "uploads/school_images/";
        $file_extension = strtolower(pathinfo($_FILES["school_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = 'school_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["school_image"]["tmp_name"], $target_file)) {
            // Delete old image if it exists and is not default
            if ($event['school_image'] != 'default_school.png') {
                $old_image = $target_dir . $event['school_image'];
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $school_image = $new_filename;
        }
    }
    
    try {
        $sql = "UPDATE events SET 
                school_name = ?, 
                location = ?, 
                city = ?, 
                event_date = ?, 
                students_count = ?, 
                school_image = ? 
                WHERE id = ? AND coordinator_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$school_name, $location, $city, $event_date, $students_count, $school_image, $event_id, $_SESSION['user_id']]);
        header("Location: events.php?msg=Event updated successfully!");
        exit();
    } catch(PDOException $e) {
        $error = "Failed to update event.";
    }
}
?>

<div class="container">
    <div class="edit-event-form card">
        <h2>Edit Event</h2>
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="school_name">School Name</label>
                <input type="text" id="school_name" name="school_name" value="<?php echo htmlspecialchars($event['school_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($event['city']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" id="event_date" name="event_date" value="<?php echo $event['event_date']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="students_count">Number of Students</label>
                <input type="number" id="students_count" name="students_count" value="<?php echo $event['students_count']; ?>" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="school_image">School Image (leave empty to keep current image)</label>
                <input type="file" id="school_image" name="school_image" accept="image/*">
                <?php if($event['school_image']): ?>
                    <div class="current-image">
                        <img src="uploads/school_images/<?php echo htmlspecialchars($event['school_image']); ?>" alt="Current school image">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-buttons">
                <a href="events.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Event</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 