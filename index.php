<!-- <?php include 'includes/navbar.php'; ?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
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
<body class="bg-gray-100 animated-background flex flex-col min-h-screen">

<!-- Content Section -->
<div class="flex flex-col justify-center items-center flex-grow py-10">
    <div class="text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-800 mb-4">Welcome to the Event Management System</h1>
        <p class="text-lg text-gray-600 mb-8">Manage events and registrations efficiently with our system.</p>

        <!-- Centered Image -->
        <div class="flex justify-center mb-8">
            <img src="includes/logo.png" alt="Event Management" class="w-[50%] max-w-md rounded-lg"> 
        </div>

        <div class="flex justify-center space-x-4">
            <a href="login.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-500 transition duration-200 transform hover:scale-105">Login</a>
            <a href="register.php" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-500 transition duration-200 transform hover:scale-105">Register</a>
        </div>
    </div>

    <!-- Conditional View for User -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
        <div class="mt-8 text-center">
            <a href="user/view_events.php" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-400 transition duration-200 transform hover:scale-105">View Events</a>
        </div>
    <?php endif; ?>
</div>


    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center">
            <p class="text-sm">© 2024 Kellen Valerie</p>
        </div>
    </footer>

</body>
</html>
