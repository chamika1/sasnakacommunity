USE volunteer_forum;

-- First let's see the current table structure
DESCRIBE forum_comments;

-- Drop and recreate the table with the correct structure
DROP TABLE forum_comments;

CREATE TABLE forum_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('volunteer', 'coordinator') NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE
); 