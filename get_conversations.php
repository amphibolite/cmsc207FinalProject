<?php
session_start();
require 'db.php';

$logged_in_user = $_SESSION['user_id'] ?? null;

if (!$logged_in_user) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

$query = "
    SELECT DISTINCT u.user_id, u.username
    FROM users u
    WHERE u.user_id != :uid AND (
        u.user_id IN (
            SELECT receiver_id FROM messages WHERE sender_id = :uid
        ) OR
        u.user_id IN (
            SELECT sender_id FROM messages WHERE receiver_id = :uid
        )
    )
";

$stmt = $pdo->prepare($query);
$stmt->execute(['uid' => $logged_in_user]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
