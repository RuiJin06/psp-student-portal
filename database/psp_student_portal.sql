CREATE DATABASE IF NOT EXISTS psp_student_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE psp_student_portal;
CREATE TABLE students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_no VARCHAR(20) NOT NULL UNIQUE,
  full_name VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  profile_image VARCHAR(40) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO students (student_no, full_name, password_hash, profile_image) VALUES
('10DIT24F1184', 'Kang Rui Jin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'kang-rui-jin.png');
-- The supplied demo password is password. Change it before public deployment.
