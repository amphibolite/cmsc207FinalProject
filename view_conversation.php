<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_user = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'] ?? null;

if (!$other_user_id) {
    echo "No user selected.";
    exit();
}

// Get the other user's name
$stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = :uid");
$stmt->execute(['uid' => $other_user_id]);
$other_user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$other_user) {
    echo "User not found.";
    exit();
}

// Get messages
$stmt = $pdo->prepare("
    SELECT sender_id, message, timestamp
    FROM messages
    WHERE (sender_id = :me AND receiver_id = :other)
       OR (sender_id = :other AND receiver_id = :me)
    ORDER BY timestamp ASC
");
$stmt->execute(['me' => $logged_in_user, 'other' => $other_user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with <?= htmlspecialchars($other_user['username']) ?></title>
    <link rel="stylesheet" href="assets/css/message.css">
</head>
<body>
    <h2>Conversation with <?= htmlspecialchars($other_user['username']) ?></h2>

    <div class="chat-container">
        <?php foreach ($messages as $msg): ?>
            <div class="chat-message <?= $msg['sender_id'] == $logged_in_user ? 'you' : 'other' ?>">
                <strong><?= $msg['sender_id'] == $logged_in_user ? 'You' : htmlspecialchars($other_user['username']) ?>:</strong><br>
                <?= htmlspecialchars($msg['message']) ?>
                <div style="font-size: 0.8em; color: #666;">
                    <?= $msg['timestamp'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" action="send_message.php" class="message-form">
        <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($other_user_id) ?>">
        <textarea name="message" required></textarea>
        <button type="submit">Send</button>
    </form>

    <p><a href="messages.php">← Back to Messages</a></p>
</body>
</html>
