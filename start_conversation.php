<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_user = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT user_id, username FROM users WHERE user_id != :uid");
$stmt->execute(['uid' => $logged_in_user]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Start Conversation</title>
</head>
<body>
    <h2>Start a New Conversation</h2>
    <form method="get" action="view_conversation.php">
        <select name="user_id">
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['user_id']) ?>">
                    <?= htmlspecialchars($user['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Start</button>
    </form>
</body>
</html>
