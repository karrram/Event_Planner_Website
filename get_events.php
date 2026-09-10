<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<p>You must be logged in.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date, time");
$stmt->execute([$user_id]);

while ($event = $stmt->fetch()) {
    echo "<div class='event'>";
    echo "<h4>" . htmlspecialchars($event['title']) . "</h4>";
    echo "<p><strong>Date:</strong> " . $event['date'] . "</p>";
    echo "<p><strong>Time:</strong> " . $event['time'] . "</p>";
    echo "<p>" . nl2br(htmlspecialchars($event['description'])) . "</p>";

    // ✅ Delete button
    echo "<form action='delete_event.php' method='POST' style='display:inline; margin-right:10px;'>
            <input type='hidden' name='event_id' value='" . $event['event_id'] . "'>
            <button type='submit'>Delete</button>
          </form>";

    // ✅ Edit button
    echo "<form action='edit_event.php' method='GET' style='display:inline;'>
            <input type='hidden' name='event_id' value='" . $event['event_id'] . "'>
            <button type='submit'>Edit</button>
          </form>";

    echo "</div><hr>";
}
?>
