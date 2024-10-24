<?php
session_start();
require '../includes/db_connection.php';

// Ensure user_id is stored in session after login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login untuk mendaftar.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id']; // Get user_id from session

    try {
        // Check if user is already registered for this event
        $checkStmt = $pdo->prepare("SELECT * FROM registrations WHERE id = ? AND event_id = ?");
        $checkStmt->execute([$user_id, $event_id]);

        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Anda telah register ke event ini.']);
            exit;
        }

        // Count the current number of participants for this event
        $participantCountStmt = $pdo->prepare("SELECT COUNT(*) as participant_count FROM registrations WHERE event_id = ?");
        $participantCountStmt->execute([$event_id]);
        $participantCount = $participantCountStmt->fetch(PDO::FETCH_ASSOC)['participant_count'];

        // Fetch the maximum number of participants from the events table
        $maxParticipantsStmt = $pdo->prepare("SELECT max_participants FROM events WHERE event_id = ?");
        $maxParticipantsStmt->execute([$event_id]);
        $maxParticipants = $maxParticipantsStmt->fetch(PDO::FETCH_ASSOC)['max_participants'];

        // Check if participant count has reached the maximum limit
        if ($participantCount >= $maxParticipants) {
            echo json_encode(['status' => 'error', 'message' => 'Partisipan event telah penuh.']);
            exit;
        }

        // Proceed with registration
        $stmt = $pdo->prepare("INSERT INTO registrations (id, event_id, registered_at) VALUES (?, ?, NOW())");
        if ($stmt->execute([$user_id, $event_id])) {
            echo json_encode(['status' => 'success']);
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log(print_r($errorInfo, true)); // Log the error
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada database.']);
        }
    } catch (Exception $e) {
        error_log($e->getMessage()); // Log the error
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
