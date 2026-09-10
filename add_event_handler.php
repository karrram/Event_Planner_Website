<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required = ['event_type', 'host', 'venue', 'event_date', 'start_time', 'end_time', 'description'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Error: $field is required");
        }
    }

    $user_id = $_SESSION['user_id'];
    $event_type = $_POST['event_type'];
    $host = $_POST['host'];
    $venue = $_POST['venue'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = $_POST['description'];

    if ($start_time >= $end_time) {
        die("Error: End time must be after start time");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO events 
                             (user_id, event_type, host, venue, event_date, start_time, end_time, description) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $user_id,
            $event_type,
            $host,
            $venue,
            $event_date,
            $start_time,
            $end_time,
            $description
        ]);

        header("Location: home.php?event_added=1");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: home.php");
    exit();
}
?>