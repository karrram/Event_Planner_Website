<?php
require 'db.php';
session_start();

$username = $_POST['username'] ?? null;
$email = $_POST['email'] ?? null;
$password_raw = $_POST['password'] ?? null;

// Validate
if (!$username || !$email || !$password_raw) {
    echo "❌ Please fill in all fields.";
    exit;
}

// Hash password
$password = password_hash($password_raw, PASSWORD_DEFAULT);

// Check for existing email
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$existingUser = $stmt->fetch();

if ($existingUser) {
    echo "⚠️ This email is already registered. Please use another one.";
    exit;
}

// Insert new user
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $password]);

// Log user in
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['user_name'] = $username;

// Redirect to home
header('Location: home.php');
exit;
?>
