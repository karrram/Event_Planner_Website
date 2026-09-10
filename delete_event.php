<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ✅ Fix: use 'event_id' instead of 'id'
    if (isset($_POST['event_id']) && is_numeric($_POST['event_id'])) {
        $event_id = (int) $_POST['event_id'];
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$event_id, $user_id]);
    }
    
    header("Location: home.php");
    exit();
}
?>
