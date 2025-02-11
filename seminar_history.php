<?php
session_start();
require_once 'config/db_connect.php';

// Check if user is logged in and is a volunteer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'volunteer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle new seminar entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_seminar'])) {
    $school_name = $_POST['school_name'];
    $seminar_date = $_POST['seminar_date'];
    
    $sql = "INSERT INTO seminar_attendance (volunteer_id, school_name, seminar_date) VALUES (?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $school_name, $seminar_date]);
        $success_msg = "Seminar attendance recorded successfully!";
    } catch(PDOException $e) {
        $error_msg = "Failed to record seminar attendance.";
    }
}

// Fetch user's seminar history
$sql = "SELECT * FROM seminar_attendance WHERE volunteer_id = ? ORDER BY seminar_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$seminars = $stmt->fetchAll();

// Get all volunteers with their seminar counts
$sql = "SELECT 
    v.id,
    v.name,
    COUNT(sa.id) as seminar_count
FROM volunteers v
LEFT JOIN seminar_attendance sa ON v.id = sa.volunteer_id
GROUP BY v.id, v.name
ORDER BY seminar_count DESC";

$all_volunteers = $pdo->query($sql)->fetchAll();

// Calculate rankings
$current_rank = 0;
$last_count = -1;
$user_rank = '-';

foreach ($all_volunteers as $key => $volunteer) {
    if ($volunteer['seminar_count'] != $last_count) {
        $current_rank = $key + 1;
        $last_count = $volunteer['seminar_count'];
    }
    
    if ($volunteer['id'] == $user_id) {
        $rank_info = $volunteer;
        $rank_info['rank'] = $volunteer['seminar_count'] > 0 ? $current_rank : '-';
    }
}

// Get top 3 volunteers
$top_volunteers = array_slice($all_volunteers, 0, 3);
foreach ($top_volunteers as $key => $volunteer) {
    $top_volunteers[$key]['rank'] = $key + 1;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Seminar History</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/seminar.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Seminar History</h2>
            <div>
                <a href="forum.php" class="btn">Back to Forum</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php if(isset($success_msg)) echo "<p class='success'>$success_msg</p>"; ?>
        <?php if(isset($error_msg)) echo "<p class='error'>$error_msg</p>"; ?>

        <!-- Rankings Section -->
        <div class="rankings-section">
            <h3>Top Volunteers</h3>
            <div class="top-volunteers">
                <?php foreach($top_volunteers as $volunteer): ?>
                    <div class="rank-card rank-<?php echo $volunteer['rank']; ?>">
                        <div class="rank-number">#<?php echo $volunteer['rank']; ?></div>
                        <div class="volunteer-name"><?php echo htmlspecialchars($volunteer['name']); ?></div>
                        <div class="seminar-count"><?php echo $volunteer['seminar_count']; ?> seminars</div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if($rank_info): ?>
                <div class="your-rank">
                    Your Rank: #<?php echo $rank_info['rank']; ?> 
                    (<?php echo $rank_info['seminar_count']; ?> seminars)
                </div>
            <?php endif; ?>
        </div>

        <!-- Add New Seminar Form -->
        <div class="add-seminar-form">
            <h3>Add New Seminar</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>School Name:</label>
                    <input type="text" name="school_name" required>
                </div>
                
                <div class="form-group">
                    <label>Seminar Date:</label>
                    <input type="date" name="seminar_date" max="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <button type="submit" name="add_seminar">Add Seminar</button>
            </form>
        </div>

        <!-- Seminar History Table -->
        <div class="seminar-history">
            <h3>Your Seminar History</h3>
            <?php if($seminars): ?>
                <table>
                    <thead>
                        <tr>
                            <th>School Name</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($seminars as $seminar): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($seminar['school_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($seminar['seminar_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No seminars recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 