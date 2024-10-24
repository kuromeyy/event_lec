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
    SELECT e.event_id, e.title, e.event_date, e.event_time, e.location 
    FROM registrations er 
    JOIN events e ON er.event_id = e.event_id 
    WHERE er.id = :user_id
    ORDER BY e.event_date DESC
");
$stmt->execute(['user_id' => $user_id]);
$events = $stmt->fetchAll();

if (isset($_POST['cancel_event_id'])) {
    $cancel_event_id = $_POST['cancel_event_id'];
    
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = :user_id AND event_id = :event_id");
    $stmt->execute(['user_id' => $user_id, 'event_id' => $cancel_event_id]);
    
    header("Location: registered_event.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Event History</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes backgroundAnimation {
            0% {
                background-color: #4A90E2;
            }
            50% {
                background-color: #50E3C2;
            }
            100% {
                background-color: #4A90E2;
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

    </style>
</head>
<body class="bg-gray-100 animated-background">

<?php include '../includes/navbar.php'; ?>

<main class="max-w-3xl mx-auto my-6">
            <header class="text-center mb-12 mt-4">
                <h1 class="text-4xl font-extrabold text-gray-800">Your Event History</h1>
            </header>
    <?php if (count($events) > 0): ?>
        <ul class="space-y-4">
            <?php foreach ($events as $event): ?>
                <li class="bg-slate-100 shadow-md rounded-lg p-4 flex flex-col justify-between">
                    <div>
                        <strong class="text-3xl text-gray-800"><?= htmlspecialchars($event['title']) ?></strong> 
                        <p class="text-md text-gray-500">
                            <?= htmlspecialchars($event['event_date']) ?>
                        </p>
                        <p class="text-md text-gray-500">
                            <?= htmlspecialchars($event['event_time']) ?> at <?= htmlspecialchars($event['location']) ?>
                        </p>
                    </div>
                    <form method="POST" action="registered_event.php" class="mt-4">
                        <input type="hidden" name="cancel_event_id" value="<?= htmlspecialchars($event['event_id']) ?>">
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white font-semibold rounded hover:bg-red-600 transition">
                            Cancel Registration
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-gray-500 text-center">No event registrations found.</p>
    <?php endif; ?>
</main>

<!-- Include the footer -->
<?php include 'footer.php'; ?>

</body>
</html>
