<?php
session_start();
require '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../view_events.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT e.title, e.event_date, e.event_time, e.location 
    FROM registrations er  
    JOIN events e ON er.event_id = e.event_id 
    WHERE er.id = :user_id  
    ORDER BY e.event_date DESC
");
$stmt->execute(['user_id' => $user_id]);
$events = $stmt->fetchAll();
?>

<?php include '../includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
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

    </style>
</head>
<body>

    <main class="animated-background min-h-screen py-12">
        <div class="max-w-5xl mx-auto">
            <header class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-800">Your Profile</h1>
                <p class="text-gray-600 mt-2">Manage your personal information and event history</p>
            </header>

            <section class="bg-white shadow-lg rounded-lg p-8 mb-12">
                <h2 class="text-3xl font-semibold text-gray-700 mb-6">Personal Information</h2>
                <div class="space-y-4">
                    <p class="text-gray-600"><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                    <p class="text-gray-600"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                </div>
                <a href="edit_profile.php" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-300 ease-in-out">
                    Edit Profile
                </a>
            </section>

            <section class="bg-white shadow-lg rounded-lg p-8">
                <h2 class="text-3xl font-semibold text-gray-700 mb-6">Your Event History</h2>
                <?php if (count($events) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($events as $event): ?>
                            <div class="p-6 bg-gray-50 rounded-lg shadow hover:shadow-md transition-shadow duration-200">
                                <strong class="text-gray-800 text-lg"><?= htmlspecialchars($event['title']) ?></strong> 
                                <div class="text-gray-600 mt-2"><?= htmlspecialchars($event['event_date']) ?>, <?= htmlspecialchars($event['event_time']) ?></div>
                                <div class="text-gray-500">at <?= htmlspecialchars($event['location']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">No event registrations found.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>
<div class="bg-white/75 py-4 text-center text-gray-600">
    © 2024 Kellen Valerie
</div>


</body>
</html>
