<?php
session_start();
require '../includes/db_connection.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get password from POST
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validate password if filled
    if (!empty($password)) {
        if ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match';
        }
    }

    // If no errors, continue to update the password
    if (count($errors) === 0) {
        if (!empty($password)) {
            // Hash the new password and update it in the database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashed_password, 'id' => $user_id]);
        }

        // Redirect after successful update
        header("Location: ../login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="animated-background flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h1 class="text-center text-2xl font-bold text-gray-800 mb-6">Change Password</h1>

        <form action="./change_password.php" method="POST">
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">New Password (optional):</label>
                <input type="password" id="password" name="password" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter new password">
            </div>

            <div class="mb-4">
                <label for="password_confirm" class="block text-gray-700 font-bold mb-2">Confirm New Password:</label>
                <input type="password" id="password_confirm" name="password_confirm" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm new password">
            </div>

            <!-- Display error messages if there are any -->
            <?php if (count($errors) > 0): ?>
                <ul class="mb-4">
                    <?php foreach ($errors as $error): ?>
                        <li class="text-red-500"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="flex flex-col space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition w-full text-sm sm:w-auto">Update Password</button>
                <a href="./change_password.php" class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 transition w-full sm:w-auto text-center">Cancel</a>
            </div>

        </form>
    </div>

</body>
</html>
