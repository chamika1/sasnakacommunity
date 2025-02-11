CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    school_name VARCHAR(255) NOT NULL,
    school_image VARCHAR(255) DEFAULT 'default_school.png',
    location TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    students_count INT NOT NULL,
    coordinator_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coordinator_id) REFERENCES coordinators(id) ON DELETE CASCADE
); 