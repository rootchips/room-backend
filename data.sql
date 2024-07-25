INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$mNsppX47WMw53tUPz2KiTuiwQqLQEwON1xpGVqlGdK9UCingqzN4.', 'admin');

INSERT INTO rooms (name, description, capacity, created_at, updated_at) VALUES
('Conference Room A', 'A large conference room equipped with a projector and whiteboard.', 20, NOW(), NOW()),
('Meeting Room B', 'A small meeting room with a round table and 6 chairs.', 6, NOW(), NOW()),
('Training Room C', 'A training room with seating for 15 and a smart TV.', 15, NOW(), NOW()),
('Executive Suite', 'A luxurious executive suite with a private bathroom and a large desk.', 2, NOW(), NOW()),
('Auditorium', 'A spacious auditorium with a stage and seating for 100 people.', 100, NOW(), NOW());
