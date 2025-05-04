<?php
require 'db.php';
session_start();

// Optional: restrict access to logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Get food items
$stmt = $pdo->query("SELECT food_item FROM food_item");
$foodItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Expired Food</title>
    <link rel="stylesheet" href="/assets/css/claim.css"> <!-- Match register_claim -->
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="card-title">Register Expired Food Item</h2>
            <form method="POST" class="form">

                <?php if (!empty($success)): ?>
                    <div class="success-message">
                        ✅ Expired Item registered successfully!
                        <a href="homepage.php" class="return-link">Go back</a>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="food_item">Select Food Item:</label>
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
                    <input type="number" name="quantity" id="quantity" step="0.01" min="0.01" required>
                </div>

                <button type="submit" class="btn">Register Expired Food</button>
            </form>
            <a href="homepage.php" class="btn back-btn">← Back to Homepage</a>

        </div>
    </div>
</body>
</html>

<?php
// Handle submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $food_item = $_POST['food_item'];
    $quantity = $_POST['quantity'];

    try {
        $stmt = $pdo->prepare("INSERT INTO expired (food_item, quantity) VALUES (:food_item, :quantity)");
        $stmt->execute([
            'food_item' => $food_item,
            'quantity' => $quantity
        ]);
        echo "<p>Expired food registered successfully! <a href='homepage.php'>Go back</a></p>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>
