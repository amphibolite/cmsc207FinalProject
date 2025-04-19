<?php
require 'db.php'; // uses $pdo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $food_item = $_POST["food_item"];
    $food_group = $_POST["food_group"];
    $perishable = $_POST["perishable"] === "true" ? true : false;
    $unit = $_POST["unit"];

    try {
        $stmt = $pdo->prepare("INSERT INTO food_item (food_item, food_group, perishable, unit) VALUES (:food_item, :food_group, :perishable, :unit)");
        $stmt->execute([
            'food_item' => $food_item,
            'food_group' => $food_group,
            'perishable' => $perishable,
            'unit' => $unit
        ]);
        echo "Food item registered successfully! <a href='homepage.php'>Go back</a>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
