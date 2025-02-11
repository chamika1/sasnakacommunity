CREATE DATABASE volunteer_forum;
USE volunteer_forum;

CREATE TABLE volunteers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_picture VARCHAR(255) DEFAULT 'default_profile.png'
);

CREATE TABLE coordinators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    password VARCHAR(255) NOT NULL,
    join_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_picture VARCHAR(255) DEFAULT 'default_profile.png'
);

CREATE TABLE forum_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('volunteer', 'coordinator') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES volunteers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES coordinators(id) ON DELETE CASCADE
);

CREATE TABLE forum_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('volunteer', 'coordinator') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES volunteers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES coordinators(id) ON DELETE CASCADE
);

CREATE TABLE seminar_attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    volunteer_id INT NOT NULL,
    school_name VARCHAR(255) NOT NULL,
    seminar_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE
);

CREATE TABLE forum_chat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    sender_type ENUM('volunteer', 'coordinator') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES volunteers(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES coordinators(id) ON DELETE CASCADE
); 