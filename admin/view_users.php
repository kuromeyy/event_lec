<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
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

    <div class="content container mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
        <h1 class="text-2xl font-bold text-gray-700 mb-4 text-center"><strong>View Users</strong></h1>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Role</th>
                        <th class="py-3 px-6 text-left">Registration Date</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100 transition duration-200">
                                <td class="py-3 px-6"><?= htmlspecialchars($user['id']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($user['name']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($user['role']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($user['created_at']) ?></td>
                                <td class="py-3 px-6">
                                    <?php if ($user['role'] != 'admin'): ?>
                                        <button onclick="confirmDeleteUser(<?= $user['id'] ?>)" class="bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600 transition duration-200">Delete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-3">No users found.</td>
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

    <script>
        function confirmDeleteUser(userId) {
            const confirmDelete = confirm('Are you sure you want to delete this user? This action cannot be undone.');
            if (confirmDelete) {
                deleteUser(userId);
            }
        }

        function deleteUser(userId) {
            fetch(`delete_user.php?id=${userId}`, {
                method: 'POST',
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to delete user');
                }
                return response.text();
            })
            .then(result => {
                alert('User deleted successfully!');
                location.reload();  
            })
            .catch(error => {
                console.error('Error deleting user:', error);
                alert('An error occurred while trying to delete the user.');
            });
        }
    </script>
</body>
</html>
