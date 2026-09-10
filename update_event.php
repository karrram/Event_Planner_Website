<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required = ['event_id', 'event_type', 'host', 'venue', 'event_date', 'start_time', 'end_time', 'description'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Error: $field is required");
        }
    }

    $event_id = $_POST['event_id'];
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
        $stmt = $pdo->prepare("UPDATE events SET 
                             event_type = ?,
                             host = ?,
                             venue = ?,
                             event_date = ?,
                             start_time = ?,
                             end_time = ?,
                             description = ?
                             WHERE event_id = ? AND user_id = ?");
        
        $stmt->execute([
            $event_type,
            $host,
            $venue,
            $event_date,
            $start_time,
            $end_time,
            $description,
            $event_id,
            $user_id
        ]);

        header("Location: home.php?update=success");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: home.php");
    exit();
}
?>
