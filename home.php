<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$userName = $user['username'] ?? 'User';

$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY event_date ASC");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Events - Event Planner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Same CSS as before (unchanged for UI) */
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
        .nav-buttons {
            display: flex;
            gap: 1rem;
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
        .add-btn {
            background-color: var(--success-color);
        }
        .logout-btn {
            background-color: var(--dark-color);
        }
        .button-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .welcome-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        h1 {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        h2 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        .event-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }
        .event-card {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .event-title {
            font-size: 1.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
            flex-grow: 1;
        }
        .countdown {
            font-size: 0.9rem;
            color: var(--secondary-color);
            background-color: rgba(79, 195, 247, 0.1);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            margin-left: 1rem;
            white-space: nowrap;
        }
        .countdown-number {
            font-weight: bold;
        }
        .event-details {
            margin: 1rem 0;
        }
        .event-details p {
            margin: 0.5rem 0;
        }
        .event-details strong {
            color: var(--primary-color);
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .action-btn {
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .download-btn { background-color: #2196F3; }
        .edit-btn { background-color: #4CAF50; }
        .delete-btn { background-color: #f44336; }
        .empty-state {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            nav {
                padding: 1rem;
            }
            .main-container {
                padding: 0 1rem;
            }
            .event-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            .countdown {
                margin-left: 0;
                align-self: flex-end;
            }
            .button-group {
                flex-direction: column;
            }
            .action-btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="add_event.php" class="button-link add-btn">
            <i class="fas fa-plus"></i> Add New Event
        </a>
        <div class="nav-buttons">
            <a href="index.html" class="button-link logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="welcome-section">
            <h1>Welcome, <?= htmlspecialchars($userName) ?></h1>
            <h2>Your Events</h2>
        </div>
        
        <div class="event-list">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <h3 class="event-title">
                                <?php 
                                $icons = [
                                    'birthday' => '🎂',
                                    'wedding' => '💍',
                                    'graduation' => '🎓',
                                    'conference' => '📊',
                                    'anniversary' => '🥂'
                                ];
                                echo $icons[$event['event_type']] . ' ' . ucfirst($event['event_type']);
                                ?>
                            </h3>
                            <div class="countdown" id="countdown-<?= $event['event_id'] ?>">
                                <span class="countdown-number">Loading...</span>
                            </div>
                        </div>
                        
                        <div class="event-details">
                            <p><strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></p>
                            <p><strong>Host:</strong> <?= htmlspecialchars($event['host']) ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
                            <p><strong>Time:</strong> <?= htmlspecialchars($event['start_time']) ?> - <?= htmlspecialchars($event['end_time']) ?></p>
                            <p><strong>Description:</strong> <?= htmlspecialchars($event['description']) ?></p>
                        </div>
                        
                        <div class="button-group">
                            <a href="download_event.php?event_id=<?= $event['event_id'] ?>" class="action-btn download-btn">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <a href="edit_event.php?event_id=<?= $event['event_id'] ?>" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="delete_event.php" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                <button type="submit" class="action-btn delete-btn">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <script>
                        function updateCountdown<?= $event['event_id'] ?>() {
                            const eventDateStr = '<?= $event['event_date'] ?>T<?= $event['start_time'] ?>';
                            const eventDate = new Date(eventDateStr);
                            const now = new Date();

                            if (isNaN(eventDate.getTime())) {
                                document.getElementById('countdown-<?= $event['event_id'] ?>').innerHTML =
                                    '<span class="countdown-number">Date error</span>';
                                return;
                            }

                            const diff = eventDate - now;

                            if (diff <= 0) {
                                document.getElementById('countdown-<?= $event['event_id'] ?>').innerHTML =
                                    '<span class="countdown-number">Event in progress</span>';
                                return;
                            }

                            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                            document.getElementById('countdown-<?= $event['event_id'] ?>').innerHTML =
                                `<span class="countdown-number">${days}d ${hours}h ${minutes}m ${seconds}s</span>`;
                        }

                        updateCountdown<?= $event['event_id'] ?>();
                        setInterval(updateCountdown<?= $event['event_id'] ?>, 1000);
                    </script>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No events found. Click "Add New Event" to get started!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
