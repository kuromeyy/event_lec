<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'user') {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll();
?>

<?php include '../includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Happening</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes backgroundAnimation {
            0% {
                background-color: #4A90E2; /* Initial color */
            }
            50% {
                background-color: #50E3C2; /* Midpoint color */
            }
            100% {
                background-color: #4A90E2; /* Back to initial color */
            }
        }
        .animated-background {
            animation: backgroundAnimation 10s ease infinite;
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        footer {
            background-color: rgba(255, 255, 255, 0.75); 
            text-align: center;
            padding: 1rem;
            font-size: 0.875rem;
            color: #6B7280;
        }
    </style>
</head>
<body class="bg-gray-100 animated-background">

    <main class="flex-grow">
            <header class="text-center mb-12 mt-8">
                <h1 class="text-4xl font-extrabold text-gray-800">Events Happening</h1>
            </header>

        <div class="grid grid-cols-1 gap-6 px-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($events as $event): ?>
                <div class="border bg-white rounded-lg shadow-md p-4 text-left">
                    <img src="../assets/images/<?= $event['banner'] ?>?v=<?= time() ?>" alt="<?= $event['title'] ?> Banner" class="w-full h-48 shadow-md object-cover rounded-md mb-4">
                    <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($event['title']) ?></h2>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="showDetails(<?= $event['event_id'] ?>)">Details</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="event-popup" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center" style="display:none;">
            <div class="relative bg-white p-12 rounded-lg shadow-lg w-11/12 max-w-lg text-center">
                <button class="absolute top-1 right-1 opacity-50 hover:opacity-100 text-xl font-bold px-3 py-3 rounded-full" onclick="closePopup()">
                    <svg width="20px" height="20px" viewBox="-0.5 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path d="M3 21.32L21 3.32001" stroke="#ff0000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M3 3.32001L21 21.32" stroke="#ff0000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
                        </g>
                    </svg>
                </button>

                <div class="rounded-md border border-slate-300 border-1 p-2 shadow-md mb-4">
                    <img id="popup-image" src="" alt="Event Image" class="w-full h-48 object-cover rounded-md">
                </div>
                <h3 id="popup-title" class="text-3xl font-semibold mb-2"></h3>
                <p id="popup-description" class="text-lg font-normal mb-2"></p>
                <p id="popup-date-time" class="text-lg font-normal mb-2"></p>
                <p id="popup-location" class="text-lg font-normal mb-2"></p>
                
                <div class="flex justify-around mt-6">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="registerEvent(<?= $event['event_id'] ?>)">Register</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        © 2024 Kellen Valerie
    </footer>

    <script>
        let currentEventId; 

        function showDetails(eventId) {
            currentEventId = eventId; 

            fetch(`get_event_details.php?event_id=${eventId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(event => {
                    const data = event.split('|'); 
                    document.getElementById('popup-title').innerText = data[0]; 
                    document.getElementById('popup-image').src = '../assets/images/' + data[1] + '?v=' + new Date().getTime();
                    document.getElementById('popup-description').innerText = data[2]; 
                    document.getElementById('popup-date-time').innerText = `Date: ${data[3]}, Time: ${data[4]}`; 
                    document.getElementById('popup-location').innerText = `Location: ${data[5]}`;
                    document.getElementById('event-popup').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error fetching event details:', error);
                });
        }

        function closePopup() {
            document.getElementById('event-popup').style.display = 'none';
        }

        function registerEvent() {
            const userId = <?= $_SESSION['user_id'] ?>; 

            fetch('register_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `event_id=${currentEventId}&user_id=${userId}` 
            })
            .then(response => {
                return response.json(); 
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('You have successfully registered for the event!');
                    closePopup(); 
                } else {
                    alert(`Registration failed: ${data.message}`); 
                }
            })
            .catch(error => {
                console.error('Error registering for event:', error);
                alert('There was an error registering for the event. Please try again.');
            });
        }
    </script>
</body>
</html>
