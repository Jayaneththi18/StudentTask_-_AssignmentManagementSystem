
# StudyFlow 🎓 — Student Task & Assignment Management System

StudyFlow is a full-stack web application designed to help students organize their academic workload and enable administrators to oversee student progress and post announcements.

## ✨ Features

### 👨‍🎓 Student Portal
- Register and log in securely
- Create, update, and track tasks and assignments by subject
- Set task types (assignment, exam, lab, project), priority levels, and due dates
- Monitor progress with a visual progress tracker
- View announcements posted by admins

### ⚙️ Admin Panel
- View and manage all registered students
- Monitor tasks and assignments across all users
- Post normal or urgent announcements to all students
- Dashboard overview of system activity

## 🛠️ Tech Stack
- **Frontend:** HTML, CSS, JavaScript, Bootstrap 5, SweetAlert2, Flatpickr
- **Backend:** PHP
- **Database:** MySQL (`studyflow_db`)

## 🚀 Getting Started

1. Clone the repository
2. Import `database.sql` into your MySQL server
3. Configure your DB credentials in `includes/config.php`
4. Serve the project via a local PHP server (e.g., XAMPP, Laragon)
5. Log in with the default admin account:
   - **Email:** admin@studyflow.com
   - **Password:** password

## 📁 Project Structure
├── admin/          # Admin dashboard (PHP)
├── student/        # Student dashboard (PHP)
├── includes/       # DB config and shared logic
├── css/            # Stylesheets
├── js/             # Client-side scripts
├── database.sql    # Database schema & seed data
├── login.html      # Login page
└── register.html   # Registration page
