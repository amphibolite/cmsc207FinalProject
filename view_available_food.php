<?php
require 'db.php';
session_start();

// Fetch available food with donor location info
$sql = "
    SELECT 
    d.food_item,
    u.username AS donor_name,
    CONCAT(u.street_address, ', ', u.barangay, ', ', u.city, ', ', u.province) AS donor_location,
    SUM(d.quantity) AS total_donated,
    COALESCE(SUM(c.quantity), 0) AS total_claimed,
    COALESCE(e.total_expired, 0) AS total_expired,
    (SUM(d.quantity) - COALESCE(SUM(c.quantity), 0) - COALESCE(e.total_expired, 0)) AS available_quantity
FROM donation d
JOIN users u ON d.donor = u.user_id
LEFT JOIN claim c 
    ON d.food_item = c.food_item AND d.donor = c.claim_point
LEFT JOIN (
    SELECT food_item, SUM(quantity) AS total_expired
    FROM expired
    GROUP BY food_item
) e ON d.food_item = e.food_item
GROUP BY d.food_item, u.username, donor_location, e.total_expired
HAVING (SUM(d.quantity) - COALESCE(SUM(c.quantity), 0) - COALESCE(e.total_expired, 0)) > 0
ORDER BY d.food_item;

";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Food</title>
    <link rel="stylesheet" href="assets/css/view_food.css">
    <style>
        .search-box {
            margin-bottom: 15px;
        }

        .search-box input {
            padding: 8px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
    <script>
        function filterTable() {
            const input = document.getElementById("locationSearch");
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll("#foodTable tbody tr");

            rows.forEach(row => {
                const location = row.cells[3].textContent.toLowerCase();
                row.style.display = location.includes(filter) ? "" : "none";
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Available Donated Food</h2>

        <div class="search-box">
            <label for="locationSearch">Search by Location:</label><br>
            <input type="text" id="locationSearch" onkeyup="filterTable()" placeholder="Enter barangay, city, or province...">
        </div>

        <?php if (count($results) > 0): ?>
            <table id="foodTable">
                <thead>
                    <tr>
                        <th>Food Item</th>
                        <th>Available Quantity</th>
                        <th>Donor</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['food_item']) ?></td>
                            <td><?= htmlspecialchars($row['available_quantity']) ?></td>
                            <td><?= htmlspecialchars($row['donor_name']) ?></td>
                            <td><?= htmlspecialchars($row['donor_location']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No available food items at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>