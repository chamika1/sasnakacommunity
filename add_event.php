<?php
$page_title = "Add New Event";
$extra_css = '<link rel="stylesheet" href="css/events.css">';
require_once 'includes/header.php';
require_once 'config/db_connect.php';

// Check if user is logged in and is a coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coordinator') {
    header("Location: login.php");
    exit();
}

// Handle event submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $school_name = $_POST['school_name'];
    $location = $_POST['location'];
    $city = $_POST['city'];
    $event_date = $_POST['event_date'];
    $students_count = $_POST['students_count'];
    
    // Handle school image upload
    $school_image = 'default_school.png';
    if (!empty($_FILES['school_image']['name'])) {
        $target_dir = "uploads/school_images/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["school_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = 'school_' . time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["school_image"]["tmp_name"], $target_file)) {
            $school_image = $new_filename;
        }
    }
    
    // Insert event into database
    $sql = "INSERT INTO events (school_name, school_image, location, city, event_date, students_count, coordinator_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$school_name, $school_image, $location, $city, $event_date, $students_count, $_SESSION['user_id']]);
        header("Location: events.php?msg=Event created successfully!");
        exit();
    } catch(PDOException $e) {
        $error = "Failed to create event. Please try again.";
    }
}
?>

<div class="container">
    <div class="add-event-form card">
        <h2>Add New Event</h2>
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="school_name">School Name</label>
                <input type="text" id="school_name" name="school_name" required>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>
            
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            
            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" id="event_date" name="event_date" required>
            </div>
            
            <div class="form-group">
                <label for="students_count">Number of Students</label>
                <input type="number" id="students_count" name="students_count" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="school_image">School Image</label>
                <input type="file" id="school_image" name="school_image" accept="image/*">
            </div>
            
            <div class="form-buttons">
                <a href="events.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Event</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 