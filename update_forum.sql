-- Add image column to forum_posts table
ALTER TABLE forum_posts 
ADD post_image VARCHAR(255) DEFAULT NULL;

-- Create comments table
CREATE TABLE forum_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('volunteer', 'coordinator') NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE
); 