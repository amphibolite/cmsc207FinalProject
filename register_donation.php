<?php
require 'db.php';
session_start();

// Optional: redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Initialize success flag
$success = false;

// Fetch food items from DB
$foodStmt = $pdo->query("SELECT food_item FROM food_item");
$foodItems = $foodStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users from DB (to select donor)
$userStmt = $pdo->query("SELECT user_id, username FROM users");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor = $_POST['donor'];
    $food_item = $_POST['food_item'];
    $quantity = $_POST['quantity'];

    try {
        $drop_off_point = $_SESSION['user_id'];

        $stmt = $pdo->prepare("INSERT INTO donation (donor, food_item, quantity, drop_off_point) 
                               VALUES (:donor, :food_item, :quantity, :drop_off_point)");
        $stmt->execute([
            'donor' => $donor,
            'food_item' => $food_item,
            'quantity' => $quantity,
            'drop_off_point' => $drop_off_point
        ]);

        $success = true;
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Donation</title>
    <link rel="stylesheet" href="/assets/css/claim.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="card-title">Add a Donation</h2>
            <form method="POST" class="form">

                <?php if ($success): ?>
                    <div class="success-message">
                        ✅ Donation submitted successfully!
                        <a href="homepage.php" class="return-link">Go back</a>
                    </div>
                <?php endif; ?>

                <?php

                if (!isset($_SESSION['user_id'])) {
                    header("Location: login.php");
                    exit();
                }

                $user_id = $_SESSION['user_id'];

                $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = :uid");
                $stmt->execute(['uid' => $user_id]);
                $current_user = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <input type="hidden" name="donor" value="<?= htmlspecialchars($user_id) ?>">

       
                <div class="form-group">
                    <label for="donor_display">Donor:</label>
                    <select id="donor_display" disabled>
                        <option><?= htmlspecialchars($current_user['username']) ?></option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="food_item">Food Item:</label>
                    <select name="food_item" id="food_item" required>
                        <?php foreach ($foodItems as $item): ?>
                            <option value="<?= htmlspecialchars($item['food_item']) ?>">
                                <?= htmlspecialchars($item['food_item']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" step="0.01" min="1" required>
                </div>

                <button type="submit" class="btn">Submit Donation</button>
            </form>
            <a href="homepage.php" class="btn back-btn">← Back to Homepage</a>
        </div>
    </div>
</body>
</html>
