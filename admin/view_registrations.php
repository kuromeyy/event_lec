<?php
session_start();
require '../includes/db_connection.php';

// Check if user is admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Fetch registrations data
$stmt = $pdo->query("
    SELECT users.name, users.email, events.title
    FROM registrations 
    JOIN users ON registrations.id = users.id 
    JOIN events ON registrations.event_id = events.event_id 
");

$registrations = $stmt->fetchAll();

// Handle Excel export
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel'); // Set content type for Excel
    header('Content-Disposition: attachment; filename="registrations.xls"'); // Suggest filename for download
    header('Cache-Control: max-age=0'); // No cache

    // Start outputting the HTML
    echo '<table border="1">'; // Using a table to structure the data for Excel
    echo '<tr><th>UserName</th><th>Email</th><th>Event Title</th></tr>'; // Column headings

    // Loop through registrations and add each one to the table
    foreach ($registrations as $registration) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($registration['name']) . '</td>';   // UserName
        echo '<td>' . htmlspecialchars($registration['email']) . '</td>';  // Email
        echo '<td>' . htmlspecialchars($registration['title']) . '</td>';  // Event Title
        echo '</tr>';
    }

    echo '</table>'; // Close the table
    exit; // End the script
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event Registrations</title>
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
        /* Flexbox-based layout to make the footer sticky */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        body {
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
    </style>
</head>
<body class="bg-gray-100 animated-background">
    <?php include '../includes/navbar.php'; ?>

    <div class="content container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-gray-700 mb-4">View Event Registrations</h1>
        
        <div class="flex justify-center mb-4">
            <a href="view_registrations.php?export=excel" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">Export to Excel</a>
        </div>

        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="w-full bg-gray-200 border-b">
                        <th class="py-2 px-4 text-left text-gray-600">UserName</th>
                        <th class="py-2 px-4 text-left text-gray-600">Email</th>
                        <th class="py-2 px-4 text-left text-gray-600">Event Title</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($registrations) > 0): ?>
                        <?php foreach ($registrations as $registration): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="py-2 px-4"><?= htmlspecialchars($registration['name']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($registration['email']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($registration['title']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="py-2 px-4 text-center">No registrations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="bg-white/75 py-4 text-center text-gray-600">
        © 2024 Kellen Valerie
    </div>
</body>
</html>
