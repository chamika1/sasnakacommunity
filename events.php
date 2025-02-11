<?php
$page_title = "Upcoming Events";
$extra_css = '<link rel="stylesheet" href="css/events.css">';
require_once 'includes/header.php';
require_once 'config/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Fetch upcoming events
$sql = "SELECT e.*, c.name as coordinator_name, c.phone as coordinator_phone, c.whatsapp as coordinator_whatsapp 
        FROM events e 
        JOIN coordinators c ON e.coordinator_id = c.id 
        WHERE e.event_date >= CURDATE()
        ORDER BY e.event_date ASC";
$upcoming_events = $pdo->query($sql)->fetchAll();

// Fetch past events
$sql = "SELECT e.*, c.name as coordinator_name, c.phone as coordinator_phone, c.whatsapp as coordinator_whatsapp 
        FROM events e 
        JOIN coordinators c ON e.coordinator_id = c.id 
        WHERE e.event_date < CURDATE()
        ORDER BY e.event_date DESC";
$past_events = $pdo->query($sql)->fetchAll();
?>

<div class="container">
    <!-- Upcoming Events Section -->
    <div class="events-section">
        <div class="section-header">
            <h2>Upcoming Events</h2>
            <?php if($_SESSION['user_type'] == 'coordinator'): ?>
                <a href="add_event.php" class="btn btn-primary">Add New Event</a>
            <?php endif; ?>
        </div>
        
        <div class="events-grid">
            <?php foreach($upcoming_events as $event): ?>
                <div class="event-card">
                    <div class="event-image">
                        <img src="<?php 
                            echo $event['school_image'] == 'default_school.png' 
                                ? 'images/default_school.png' 
                                : 'uploads/school_images/' . htmlspecialchars($event['school_image']); 
                        ?>" alt="School Image">
                        <div class="event-date">
                            <span class="date-day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                            <span class="date-month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                        </div>
                    </div>
                    <div class="event-details">
                        <h3><?php echo htmlspecialchars($event['school_name']); ?></h3>
                        
                        <div class="event-info">
                            <div class="info-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                </svg>
                                <span><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <div class="info-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                </svg>
                                <span><?php echo $event['students_count']; ?> Students</span>
                            </div>
                            <div class="info-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                </svg>
                                <a href="tel:<?php echo $event['coordinator_phone']; ?>"><?php echo $event['coordinator_phone']; ?></a>
                            </div>
                        </div>

                        <div class="coordinator-info">
                            <strong>Coordinator:</strong> <?php echo htmlspecialchars($event['coordinator_name']); ?>
                        </div>
                    </div>
                    <?php if($_SESSION['user_type'] == 'coordinator' && $event['coordinator_id'] == $_SESSION['user_id']): ?>
                        <div class="event-actions">
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-secondary">Edit</a>
                            <a href="delete_event.php?id=<?php echo $event['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Past Events Section -->
    <div class="events-section past-events">
        <h2>Past Events</h2>
        <div class="events-grid">
            <?php foreach($past_events as $event): ?>
                <div class="event-card past">
                    <div class="event-image">
                        <img src="<?php 
                            echo $event['school_image'] == 'default_school.png' 
                                ? 'images/default_school.png' 
                                : 'uploads/school_images/' . htmlspecialchars($event['school_image']); 
                        ?>" alt="School Image">
                        <div class="event-date">
                            <span class="date-day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                            <span class="date-month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                        </div>
                    </div>
                    <div class="event-details">
                        <h3><?php echo htmlspecialchars($event['school_name']); ?></h3>
                        
                        <div class="event-info">
                            <div class="info-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                </svg>
                                <span><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <div class="info-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                </svg>
                                <span><?php echo $event['students_count']; ?> Students</span>
                            </div>
                            <div class="info-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                </svg>
                                <a href="tel:<?php echo $event['coordinator_phone']; ?>"><?php echo $event['coordinator_phone']; ?></a>
                            </div>
                        </div>

                        <div class="coordinator-info">
                            <strong>Coordinator:</strong> <?php echo htmlspecialchars($event['coordinator_name']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 