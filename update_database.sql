-- Add profile_picture column to volunteers table
ALTER TABLE volunteers 
ADD profile_picture VARCHAR(255) DEFAULT 'default_profile.png';

-- Add profile_picture column to coordinators table
ALTER TABLE coordinators 
ADD profile_picture VARCHAR(255) DEFAULT 'default_profile.png'; 