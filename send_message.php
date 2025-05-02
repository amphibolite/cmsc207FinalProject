<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$receiver_id || $message === '') {
    echo "Missing receiver or message content.";
    exit();
}

// Insert message
$stmt = $pdo->prepare("
    INSERT INTO messages (sender_id, receiver_id, message)
    VALUES (:sender, :receiver, :message)
");
$stmt->execute([
    'sender' => $sender_id,
    'receiver' => $receiver_id,
    'message' => $message
]);

// Redirect back to conversation
header("Location: view_conversation.php?user_id=" . urlencode($receiver_id));
exit();
