<?php
require 'db.php';
session_start();

// Optional: redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Fetch food items from DB
$foodStmt = $pdo->query("SELECT food_item FROM food_item");
$foodItems = $foodStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users from DB (to select donor)
$userStmt = $pdo->query("SELECT user_id, username FROM users");
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Donation</title>
</head>
<body>
    <h2>Add a Donation</h2>
    <form method="POST">
        <!-- Select donor -->
        <label for="donor">Select Donor:</label><br>
        <select name="donor" required>
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

        echo "<p>Donation added successfully! <a href='homepage.php'>Go back</a></p>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>
