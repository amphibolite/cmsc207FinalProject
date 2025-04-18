<?php
$host = "localhost";
$dbname = "cmsc207_db";
$user = "amphibolite";
$password = "";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
