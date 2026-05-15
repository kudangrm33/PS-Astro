<?php
session_start();

require __DIR__ . '/../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :u LIMIT 1');
  $stmt->execute([':u' => $username]);
  $admin = $stmt->fetch();

  if ($admin && password_verify($password, $admin['password_hash'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    header('Location: index.php');
    exit;
  } else {
    $error = 'Username atau password salah.';
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Admin - Ps Astro</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      background-color: #010101;
      color: #fff;
      font-family: "Poppins", sans-serif;
    }
    .admin-login-box {
      max-width: 400px;
      margin: 8rem auto;
      padding: 2rem;
      background: #111;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.6);
    }
    .admin-login-box h1 {
      text-align: center;
      margin-bottom: 1.5rem;
    }
    .admin-login-box label {
      display: block;
      margin-top: 0.7rem;
      font-size: 0.9rem;
    }
    .admin-login-box input {
      width: 100%;
      padding: 0.6rem 0.8rem;
      margin-top: 0.2rem;
      border-radius: 0.4rem;
      border: 1px solid #444;
      background-color: #000;
      color: #fff;
    }
    .admin-login-box button {
      margin-top: 1rem;
      width: 100%;
      padding: 0.7rem;
      border-radius: 999px;
      border: none;
      background-color: #b6895b;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
    }
    .admin-error {
      margin-top: 0.7rem;
      color: #ff6b6b;
      font-size: 0.9rem;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="admin-login-box">
    <h1>Login Admin</h1>
    <form method="post">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Login</button>

      <?php if ($error): ?>
        <p class="admin-error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
