<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Event Planner</title>
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
        
        .back-btn {
            background-color: var(--secondary-color);
        }
        
        .logout-btn {
            background-color: var(--dark-color);
        }
        
        .button-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .form-header h2 {
            color: var(--secondary-color);
            font-size: 2rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--secondary-color);
        }
        
        input, textarea, select {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1.5rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 195, 247, 0.2);
        }
        
        .time-container {
            display: flex;
            gap: 1.5rem;
        }
        
        .time-container > div {
            flex: 1;
        }
        
        button[type="submit"] {
            background-color: var(--success-color);
            color: white;
            padding: 0.8rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        button[type="submit"]:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            nav {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .form-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .time-container {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="home.php" class="button-link back-btn">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
        <div class="nav-buttons">
            <a href="logout.php" class="button-link logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="form-container">
        <div class="form-header">
            <h2>Add New Event</h2>
        </div>
        
        <form action="add_event_handler.php" method="POST">
            <label for="event_type">Event Type*</label>
            <select name="event_type" id="event_type" required>
                <option value="">Select Event Type</option>
                <option value="birthday">🎂 Birthday</option>
                <option value="wedding">💍 Wedding</option>
                <option value="graduation">🎓 Graduation</option>
                <option value="conference">📊 Business Conference</option>
                <option value="anniversary">🥂 Anniversary</option>
            </select>

            <label for="host">Host*</label>
            <input type="text" name="host" id="host" required>

            <label for="venue">Venue*</label>
            <select name="venue" id="venue" required>
                <option value="">Select Venue</option>
                <option value="The Ritz-Carlton">The Ritz-Carlton</option>
                <option value="Marriott Grand Ballroom">Marriott Grand Ballroom</option>
                <option value="Hilton Convention Center">Hilton Convention Center</option>
                <option value="Four Seasons Hotel">Four Seasons Hotel</option>
                <option value="Hyatt Regency">Hyatt Regency</option>
                <option value="Sheraton Grand">Sheraton Grand</option>
                <option value="Waldorf Astoria">Waldorf Astoria</option>
                <option value="InterContinental">InterContinental</option>
                <option value="Westin Hotel">Westin Hotel</option>
                <option value="Mandarin Oriental">Mandarin Oriental</option>
            </select>

            <label for="event_date">Event Date*</label>
            <input type="date" name="event_date" id="event_date" required>

            <div class="time-container">
                <div>
                    <label for="start_time">Start Time*</label>
                    <input type="time" name="start_time" id="start_time" step="300" required>
                </div>
                <div>
                    <label for="end_time">End Time*</label>
                    <input type="time" name="end_time" id="end_time" step="300" required>
                </div>
            </div>

            <label for="description">Description*</label>
            <textarea name="description" id="description" rows="4" required></textarea>

            <button type="submit">
                <i class="fas fa-calendar-plus"></i> Add Event
            </button>
        </form>
    </div>
</body>
</html>