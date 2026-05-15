<?php
// make_admin.php
require __DIR__ . '/config.php';

// --- silakan atur username & password di sini ---
$username = 'admin';
$password = 'admin123';
// -----------------------------------------------

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
if (!$stmt) {
    die('Gagal prepare: ' . $conn->error);
}

$stmt->bind_param('ss', $username, $hash);

if ($stmt->execute()) {
    echo "Admin berhasil dibuat.<br>";
    echo "Username: <b>$username</b><br>";
    echo "Password: <b>$password</b><br>";
} else {
    echo "Gagal insert admin: " . $stmt->error;
}

$stmt->close();
$conn->close();
