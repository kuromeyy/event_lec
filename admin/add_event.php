<?php
session_start();
require '../includes/db_connection.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$uploadError = '';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'bmp', 'gif'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];
    $status = 'open';

    $imageName = '';
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $image = $_FILES['event_image'];
        $image_ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($image_ext), $allowed_extensions)) {
            $imageName = uniqid() . '.' . $image_ext;
            $imagePath = '../assets/images/' . $imageName;
            move_uploaded_file($image['tmp_name'], $imagePath);
        } else {
            $uploadError = 'Invalid file type for event image. Only jpg, jpeg, png, svg, webp, bmp, gif files are allowed.';
        }
    }

    $bannerName = '';
    if (isset($_FILES['event_banner']) && $_FILES['event_banner']['error'] == 0) {
        $banner = $_FILES['event_banner'];
        $banner_ext = pathinfo($banner['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($banner_ext), $allowed_extensions)) {
            $bannerName = uniqid() . '_banner.' . $banner_ext;
            $bannerPath = '../assets/images/' . $bannerName;
            move_uploaded_file($banner['tmp_name'], $bannerPath);
        } else {
            $uploadError = 'Invalid file type for event banner. Only jpg, jpeg, png, svg, webp, bmp, gif files are allowed.';
        }
    }

    if (empty($uploadError)) {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, max_participants, status, image, banner) VALUES (:title, :description, :event_date, :event_time, :location, :max_participants, :status, :image, :banner)");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'event_date' => $event_date,
            'event_time' => $event_time,
            'location' => $location,
            'max_participants' => $max_participants,
            'status' => $status,
            'image' => $imageName,
            'banner' => $bannerName
        ]);
        header("Location: manage_events.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Event</title>
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
<body class="bg-gray-100 animated-background">
<?php include '../includes/navbar.php'; ?>

<div class="max-w-lg mx-auto my-10 p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center text-gray-700 mb-6">Add New Event</h1>

    <?php if (!empty($uploadError)): ?>
        <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($uploadError) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Title:</label>
            <input type="text" name="title" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Description:</label>
            <textarea name="description" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" rows="4"></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Date:</label>
            <input type="date" name="event_date" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Time:</label>
            <input type="time" name="event_time" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Location:</label>
            <input type="text" name="location" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Max Participants:</label>
            <input type="number" name="max_participants" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Image (Optional):</label>
            <input type="file" name="event_image" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif" class="w-full p-3 border border-gray-300 rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Banner (Optional):</label>
            <input type="file" name="event_banner" accept=".jpg,.jpeg,.png,.svg,.webp,.bmp,.gif" class="w-full p-3 border border-gray-300 rounded-lg">
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white font-bold py-3 rounded-lg shadow hover:bg-blue-600 transition duration-200 ease-in-out transform hover:scale-105">Add Event</button>
    </form>
</div>

<!-- Footer -->
<div class="bg-white/75 py-4 text-center text-gray-600">
    © 2024 Kellen Valerie
</div>
</body>
</html>
