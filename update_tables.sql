-- Add profile_picture column to volunteers table
ALTER TABLE volunteers 
ADD profile_picture VARCHAR(255) DEFAULT 'default_profile.png';

-- Add profile_picture column to coordinators table
ALTER TABLE coordinators 
ADD profile_picture VARCHAR(255) DEFAULT 'default_profile.png';

-- Drop the existing forum_chat table
DROP TABLE IF EXISTS forum_chat;

-- Create the forum_chat table with a new structure
CREATE TABLE forum_chat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    sender_type ENUM('volunteer', 'coordinator') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chat_sender_check CHECK (
        CASE sender_type
            WHEN 'volunteer' THEN EXISTS (SELECT 1 FROM volunteers WHERE id = sender_id)
            WHEN 'coordinator' THEN EXISTS (SELECT 1 FROM coordinators WHERE id = sender_id)
        END
    )
); 