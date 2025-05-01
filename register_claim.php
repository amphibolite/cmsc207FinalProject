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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Claim</title>
    <link rel="stylesheet" href="/assets/css/claim.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="card-title">Register a Food Claim</h2>
            <form method="POST" class="form">

                <div class="form-group">
                    <label for="recipient">Select Recipient:</label>
                    <select name="recipient" id="recipient" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['user_id'] ?>">
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                        <?php endforeach; ?>
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

                <button type="submit" class="btn">Submit Claim</button>
            </form>
        </div>
    </div>
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
