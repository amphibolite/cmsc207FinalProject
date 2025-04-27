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
<html>
<head>
    <title>Register Expired Food</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <h2>Register Expired Food Item</h2>
    <form method="POST">

        <label for="food_item">Select Food Item:</label><br>
        <select name="food_item" required>
            <?php foreach ($foodItems as $item): ?>
                <option value="<?= htmlspecialchars($item['food_item']) ?>">
                    <?= htmlspecialchars($item['food_item']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="quantity">Quantity:</label><br>
        <input type="number" name="quantity" step="0.01" required><br><br>

        <input type="submit" value="Submit">
    </form>
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
