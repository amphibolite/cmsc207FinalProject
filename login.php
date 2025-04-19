<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Get user from DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["username"] = $user["username"];
        // echo "Login successful! Welcome, " . htmlspecialchars($user['first_name']) . "!";
        // Redirect to dashboard if needed
        // header("Location: dashboard.php");
        header("Location: homepage.php");
    } else {
        echo "Invalid username or password.";
    }
}
?>
