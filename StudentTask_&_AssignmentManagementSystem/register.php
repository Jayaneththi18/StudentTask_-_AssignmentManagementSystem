<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = 'student'; 

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            if ($stmt->execute([$name, $email, $hash, $role])) {
                header('Location: index.php?registered=1');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — StudyFlow</title>
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
    <h2>Register</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="index.php">Login</a></p>
  </div>
</div>
</body>
</html>
