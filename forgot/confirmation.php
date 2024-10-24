<?php
session_start();
require '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $phone) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ./change_password.php");
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
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
<body class="animated-background flex items-center justify-center min-h-screen bg-gray-100">

    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Verify Your Account</h1>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                <input type="email" name="email" id="email" required class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter your email">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone:</label>
                <input type="text" name="phone" id="phone" required class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter your phone number">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-500 transition duration-200 transform hover:scale-105">Next</button>
        </form>

        <?php if (isset($error)): ?>
            <p class="text-red-500 text-sm mt-4 text-center"><?= $error ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
