<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $due = trim($_POST['due'] ?? '');
    $priority = trim($_POST['priority'] ?? 'normal');

    if ($title && $subject && $due) {
        
        $stmt = $pdo->prepare("INSERT INTO assignments (title, subject, due_date, priority) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $subject, $due, $priority]);
        header('Location: dashboard.php?success=1');
        exit;
    } else {
        header('Location: dashboard.php?error=1');
        exit;
    }
}
header('Location: dashboard.php');
exit;
