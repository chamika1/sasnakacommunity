-- Insert dummy coordinators
INSERT INTO coordinators (name, email, password, phone, whatsapp, address, profile_picture, join_date) VALUES
('John Smith', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', '1234567890', '123 Main St, City', 'default_profile.png', CURRENT_TIMESTAMP),
('Sarah Johnson', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2345678901', '2345678901', '456 Oak Ave, Town', 'default_profile.png', CURRENT_TIMESTAMP),
('Michael Brown', 'michael@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3456789012', '3456789012', '789 Pine Rd, Village', 'default_profile.png', CURRENT_TIMESTAMP);

-- Insert dummy volunteers
INSERT INTO volunteers (name, email, password, phone, whatsapp, address, profile_picture) VALUES
('Emma Wilson', 'emma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '4567890123', '4567890123', '321 Elm St, City', 'default_profile.png'),
('David Lee', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5678901234', '5678901234', '654 Maple Dr, Town', 'default_profile.png'),
('Lisa Chen', 'lisa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '6789012345', '6789012345', '987 Cedar Ln, Village', 'default_profile.png');

-- Insert dummy events
INSERT INTO events (coordinator_id, school_name, location, event_date, students_count, school_image, city) VALUES
(1, 'Springfield Elementary', '123 School St, Springfield', '2024-04-15', 150, 'default_school.png', 'Springfield'),
(2, 'Riverside High School', '456 River Rd, Riverside', '2024-04-20', 200, 'default_school.png', 'Riverside'),
(3, 'Mountain View Academy', '789 Mountain Ave, Heights', '2024-04-25', 175, 'default_school.png', 'Heights'),
(1, 'Sunset Middle School', '321 Sunset Blvd, Westtown', '2024-05-01', 160, 'default_school.png', 'Westtown'),
(2, 'Valley Grammar School', '654 Valley Way, Eastside', '2024-05-05', 140, 'default_school.png', 'Eastside');

-- Insert dummy forum posts for volunteers
INSERT INTO forum_posts (user_id, user_type, title, content) VALUES
(1, 'volunteer', 'Tips for First-Time Volunteers', 'Here are some helpful tips for those just starting out...'),
(2, 'volunteer', 'My First Experience', 'I wanted to share my amazing experience from yesterday''s event...'),
(3, 'volunteer', 'Looking for Carpool', 'Anyone heading to the Springfield event next week?');

-- Insert dummy forum posts for coordinators (need to insert coordinators first)
INSERT INTO forum_posts (user_id, user_type, title, content) VALUES
(1, 'coordinator', 'Welcome to Our Community!', 'Hello everyone! Let''s make a difference in our community together.'),
(2, 'coordinator', 'Upcoming Event Planning', 'We need volunteers for next month''s events. Please check the schedule.'),
(3, 'coordinator', 'Thank You Volunteers!', 'A big thank you to all volunteers who helped this week.');

-- Insert dummy forum chat messages
INSERT INTO forum_chat (sender_id, sender_type, message) VALUES
(1, 'coordinator', 'Hello everyone! How is everyone doing today?'),
(1, 'volunteer', 'Hi! I''m excited to be here!'),
(2, 'coordinator', 'Don''t forget about tomorrow''s event!'),
(2, 'volunteer', 'I''ll be there! What time should we arrive?'),
(3, 'coordinator', 'Please arrive 30 minutes before the scheduled time.'),
(3, 'volunteer', 'Thanks for the information!');

-- Note: All passwords in this dummy data are 'password' (hashed)
-- You can login with any email using 'password' as the password 