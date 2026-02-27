
CREATE DATABASE IF NOT EXISTS studyflow_db;
USE studyflow_db;

CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,          
    role        ENUM('student','admin') DEFAULT 'student',
    student_id  VARCHAR(20),                    
    course      VARCHAR(100),
    avatar      VARCHAR(255),                   
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subjects (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    code        VARCHAR(30),
    icon        VARCHAR(10) DEFAULT '📚',
    color       VARCHAR(20) DEFAULT '#4f8ef7',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    subject_id  INT,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    type        ENUM('task','assignment','exam','lab','project') DEFAULT 'task',
    priority    ENUM('high','medium','low') DEFAULT 'medium',
    status      ENUM('pending','in_progress','done') DEFAULT 'pending',
    progress    INT DEFAULT 0,                 
    due_date    DATE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL
);

CREATE TABLE announcements (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT NOT NULL,
    title       VARCHAR(255) NOT NULL,
    body        TEXT,
    priority    ENUM('normal','urgent') DEFAULT 'normal',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role, student_id, course) VALUES
('Admin User', 'admin@studyflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ADMIN001', 'Administration');

INSERT INTO subjects (name, code, icon, color) VALUES
('Software Design & Dev', 'SEN4002', '💻', '#6ef0c8'),
('Database Systems',      'DBS3001', '🗄️', '#6eb4f0'),

