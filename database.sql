CREATE DATABASE IF NOT EXISTS pending_works_db;
USE pending_works_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  role ENUM('admin') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default login: admin / admin123
INSERT INTO users (username, password, full_name, role)
VALUES ('admin', '$2y$10$kJ5I/tqtSnTynN/2AkG1Y.VhPsRM/U00q0V3HdjyZuaPBnDFhbljy', 'System Administrator', 'admin')
ON DUPLICATE KEY UPDATE username = username;

CREATE TABLE IF NOT EXISTS pending_works (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_no VARCHAR(30) NOT NULL UNIQUE,
  work_date DATE NOT NULL,
  department VARCHAR(100) NOT NULL,
  requestor VARCHAR(120) NOT NULL,
  location VARCHAR(160) NULL,
  contact_no VARCHAR(60) NULL,
  category VARCHAR(100) NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT,
  priority ENUM('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  assigned_to VARCHAR(120),
  status ENUM('Pending','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  remarks TEXT,
  completed_date DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO pending_works
(job_no, work_date, department, requestor, category, title, description, priority, assigned_to, status, remarks)
VALUES
('JO-2026-0001', CURDATE(), 'MIS', 'Front Office', 'Internet Connection', 'Check unstable Wi-Fi connection', 'Guest reported intermittent internet connection.', 'High', 'Mark Laurence', 'Pending', ''),
('JO-2026-0002', CURDATE(), 'FNB', 'Cafe', 'Printer', 'Thermal printer not printing', 'POS printer needs checking.', 'Medium', 'Mark Laurence', 'In Progress', 'Initial checking done.'),
('JO-2026-0003', CURDATE(), 'ENGR', 'Engineering', 'CCTV', 'Review CCTV playback', 'Need playback for hallway camera.', 'Low', 'Mark Laurence', 'Completed', 'Done');


-- If your database already exists, run this update only once:
-- ALTER TABLE pending_works ADD COLUMN location VARCHAR(160) NULL AFTER requestor;
-- ALTER TABLE pending_works ADD COLUMN contact_no VARCHAR(60) NULL AFTER location;
