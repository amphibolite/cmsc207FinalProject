<?php
require 'db.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Fetch food items
$foodStmt = $pdo->query("SELECT food_item FROM food_item");
$foodItems = $foodStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users (recipients)
$userStmt = $pdo->query("SELECT user_id, username FROM users");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Claim</title>
</head>
<body>
    <h2>Register a Food Claim</h2>
    <form method="POST">

        <!-- Select recipient -->
        <label for="recipient">Select Recipient:</label><br>
        <select name="recipient" required>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>">
                    <?= htmlspecialchars($user['username']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Select food item -->
        <label for="food_item">Food Item:</label><br>
        <select name="food_item" required>
            <?php foreach ($foodItems as $item): ?>
                <option value="<?= htmlspecialchars($item['food_item']) ?>">
                    <?= htmlspecialchars($item['food_item']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- Enter quantity -->
        <label for="quantity">Quantity:</label><br>
        <input type="number" name="quantity" step="0.01" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>

<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient = $_POST['recipient'];
    $food_item = $_POST['food_item'];
    $quantity = $_POST['quantity'];
    $claim_point = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO claim (recipient, claim_point, food_item, quantity) 
                               VALUES (:recipient, :claim_point, :food_item, :quantity)");
        $stmt->execute([
            'recipient' => $recipient,
            'claim_point' => $claim_point,
            'food_item' => $food_item,
            'quantity' => $quantity
        ]);
        echo "<p>Claim registered successfully! <a href='homepage.php'>Go back</a></p>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>
