<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_user = $_SESSION['user_id'];

// Fetch all distinct users the logged-in user has messaged or received messages from
$stmt = $pdo->prepare("
    SELECT DISTINCT u.user_id, u.username
    FROM users u
    JOIN (
        SELECT receiver_id AS user_id FROM messages WHERE sender_id = :me
        UNION
        SELECT sender_id AS user_id FROM messages WHERE receiver_id = :me
    ) AS contacts ON u.user_id = contacts.user_id
    WHERE u.user_id != :me
");
$stmt->execute(['me' => $logged_in_user]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Messages</title>
    <link rel="stylesheet" href="assets/css/messages.css">
</head>
<body>
    <h2>Messages</h2>

    <div class="message-list">
        <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
                <a class="message-card" href="view_conversation.php?user_id=<?= $user['user_id'] ?>">
                    <div class="username"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="view-link">View Conversation →</div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-messages">You don't have any conversations yet.</p>
        <?php endif; ?>
    </div>

    <p><a class="new-message-btn" href="start_conversation.php">+ Message Someone New</a></p>
</body>
</html>
