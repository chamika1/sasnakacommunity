USE volunteer_forum;

DROP TABLE IF EXISTS forum_chat;

CREATE TABLE forum_chat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    sender_type ENUM('volunteer', 'coordinator') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 