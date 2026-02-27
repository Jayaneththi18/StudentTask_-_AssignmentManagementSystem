<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
        
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: student/dashboard.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — StudyFlow</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-left">
    <div class="logo-wrap">
      <div class="logo-icon-big">🎓</div>
      <h1>StudyFlow</h1>
      <p>Your smart student task & assignment management system</p>
    </div>
  </div>
  <div class="auth-right">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form action="index.php" method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.html">Register</a></p>
  </div>
</div>
</body>
</html>
