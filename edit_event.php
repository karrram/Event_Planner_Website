<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

if (!isset($_GET['event_id'])) {
    echo "Event ID is missing.";
    exit();
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND user_id = ?");
$stmt->execute([$event_id, $user_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "Event not found or access denied.";
    exit();
}

$venues = [
    "The Ritz-Carlton",
    "Marriott Grand Ballroom",
    "Hilton Convention Center",
    "Four Seasons Hotel",
    "Hyatt Regency",
    "Sheraton Grand",
    "Waldorf Astoria",
    "InterContinental",
    "Westin Hotel",
    "Mandarin Oriental"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc3f7;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        nav {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .button-link {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .button-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .add-btn {
            background-color: var(--success-color);
        }
        .logout-btn {
            background-color: var(--dark-color);
        }
        .form-container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 1rem;
            color: var(--secondary-color);
        }
        select, input[type="text"], input[type="date"], input[type="time"], textarea {
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        textarea {
            resize: vertical;
        }
        .time-container {
            display: flex;
            gap: 1rem;
        }
        .time-container div {
            flex: 1;
        }
        button[type="submit"] {
            margin-top: 2rem;
            background-color: var(--success-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .time-container {
                flex-direction: column;
            }
            nav {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="home.php" class="button-link"><i class="fas fa-arrow-left"></i> Back</a>
        <h2>Edit Event</h2>
        <a href="index.html" class="button-link logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <div class="form-container">
        <form action="update_event.php" method="POST">
            <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">

            <label for="event_type">Event Type*</label>
            <select name="event_type" id="event_type" required>
                <option value="birthday" <?= $event['event_type'] == 'birthday' ? 'selected' : '' ?>>🎂 Birthday</option>
                <option value="wedding" <?= $event['event_type'] == 'wedding' ? 'selected' : '' ?>>💍 Wedding</option>
                <option value="graduation" <?= $event['event_type'] == 'graduation' ? 'selected' : '' ?>>🎓 Graduation</option>
                <option value="conference" <?= $event['event_type'] == 'conference' ? 'selected' : '' ?>>📊 Business Conference</option>
                <option value="anniversary" <?= $event['event_type'] == 'anniversary' ? 'selected' : '' ?>>🥂 Anniversary</option>
            </select>

            <label for="host">Host*</label>
            <input type="text" name="host" id="host" value="<?= htmlspecialchars($event['host']) ?>" required>

            <label for="venue">Venue*</label>
            <select name="venue" id="venue" required>
                <?php foreach ($venues as $v): ?>
                    <option value="<?= $v ?>" <?= $event['venue'] == $v ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>

            <label for="event_date">Event Date*</label>
            <input type="date" name="event_date" id="event_date" value="<?= $event['event_date'] ?>" required>

            <div class="time-container">
                <div>
                    <label for="start_time">Start Time*</label>
                    <input type="time" name="start_time" id="start_time" value="<?= $event['start_time'] ?>" required>
                </div>
                <div>
                    <label for="end_time">End Time*</label>
                    <input type="time" name="end_time" id="end_time" value="<?= $event['end_time'] ?>" required>
                </div>
            </div>

            <label for="description">Description*</label>
            <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>

            <button type="submit"><i class="fas fa-save"></i> Update Event</button>
        </form>
    </div>
</body>
</html>
