<?php
require 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Get user's city
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT city FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

$city = $user['city'];

// Compute available food
$query = "
SELECT 
    fi.food_item,
    COALESCE(SUM(d.quantity), 0) AS total_donated,
    COALESCE(SUM(c.quantity), 0) AS total_claimed,
    COALESCE(SUM(e.quantity), 0) AS total_expired,
    COALESCE(SUM(d.quantity), 0) - COALESCE(SUM(c.quantity), 0) - COALESCE(SUM(e.quantity), 0) AS available
FROM food_item fi
LEFT JOIN donation d ON fi.food_item = d.food_item
    AND d.drop_off_point IN (SELECT user_id FROM users WHERE city = :city)
LEFT JOIN claim c ON fi.food_item = c.food_item
    AND c.claim_point IN (SELECT user_id FROM users WHERE city = :city)
LEFT JOIN expired e ON fi.food_item = e.food_item
GROUP BY fi.food_item
HAVING COALESCE(SUM(d.quantity), 0) - COALESCE(SUM(c.quantity), 0) - COALESCE(SUM(e.quantity), 0) > 0
ORDER BY available DESC;
";

$stmt = $pdo->prepare($query);
$stmt->execute(['city' => $city]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Food in My City</title>
</head>
<body>
    <h2>Available Food in <?= htmlspecialchars($city) ?></h2>

    <?php if (count($results) === 0): ?>
        <p>No available food in your city.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Food Item</th>
                <th>Available Quantity</th>
            </tr>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['food_item']) ?></td>
                    <td><?= htmlspecialchars($row['available']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <br><a href="homepage.php">Back to Homepage</a>
</body>
</html>
