# SASNA Community Forum

A dynamic web-based community platform designed for SASNA volunteers and coordinators to interact, share experiences, and manage seminar activities. This platform facilitates communication and coordination between volunteers and coordinators who are working to provide educational seminars to schools across Sri Lanka.

## Repository

```bash
git clone https://github.com/chamika1/sasnakacommunity.git
```

## Features

### User Management
- Separate registration and login for volunteers and coordinators
- Profile management with customizable profile pictures
- Secure authentication system

### Forum Features
- Create and view forum posts
- Image upload support for posts
- Comment system on posts
- Post management (coordinators can delete posts)
- Rich text formatting and responsive design

### Live Chat
- Real-time chat functionality
- Profile pictures in chat messages
- Message history
- Automatic refresh every 5 seconds

### Seminar Management
- Track seminar attendance for volunteers
- Leaderboard system showing top volunteers
- Personal seminar history
- Date-based seminar recording

### Coordinator Features
- Event management system
- School information tracking
- Student count monitoring
- Event date scheduling

## Technical Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- GD Library for image processing
- PDO PHP Extension
- Modern web browser with JavaScript enabled

## Installation

1. Clone the repository:
```bash
git clone https://github.com/chamika1/sasnakacommunity.git
```

2. Import the database structure:

mysql -u your_username -p < database.sql


3. Configure database connection:
   - Navigate to `config/db_connect.php`
   - Update the following variables:
     ```php
     $host = 'your_host';
     $dbname = 'volunteer_forum';
     $username = 'your_username';
     $password = 'your_password';
     ```

4. Set up file permissions:

bash
chmod 755 -R /path/to/project
chmod 777 -R uploads/

5. Create required directories:

bash
mkdir -p uploads/forum_images
mkdir -p uploads/profile_pictures
mkdir -p uploads/school_images

## Directory Structure

volunteer_forum/
├── config/
│ └── db_connect.php
├── css/
│ ├── style.css
│ ├── forum.css
│ ├── chat.css
│ └── ...
├── includes/
│ ├── header.php
│ └── footer.php
├── uploads/
│ ├── forum_images/
│ ├── profile_pictures/
│ └── school_images/
├── images/
│ └── logo.png
└── various PHP files



## Security Features

- Password hashing using PHP's password_hash()
- PDO prepared statements for SQL injection prevention
- XSS prevention through htmlspecialchars()
- File upload validation and sanitization
- Session-based authentication

## Database Schema

The system uses several interconnected tables:
- volunteers
- coordinators
- forum_posts
- forum_comments
- forum_chat
- seminar_attendance

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Built for SUSN Community
- Uses Bootstrap icons
- jQuery for enhanced functionality
- Modern CSS features for animations and styling

## Support

For support, please email [rasanjanachamika@gmail.com] or open an issue in the repository: https://github.com/chamika1/sasnakacommunity/issues

## About SASNA

SASNA is a volunteer organization dedicated to providing educational seminars to schools across Sri Lanka. Our mission is to empower students through knowledge sharing and interactive learning experiences. This community platform helps coordinate our volunteer efforts and maintains a vibrant community of dedicated educators and volunteers.

This README provides a comprehensive overview of the project, including its features, installation instructions, and technical requirements. You may want to customize certain sections (like the repository URL, support email, etc.) based on your specific needs.
The README is formatted in Markdown and includes sections that would be helpful for both users and developers who might work with the system. It covers all major features present in the codebase while maintaining a clean, professional structure.

