<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header Section -->
    <header class="dashboard-header">
        <div class="container">
            <div class="logo">
                <img src="assets/images/logo.png" alt="FoodShare Logo">
                <span>FoodShare</span>
            </div>
            <nav class="nav-links">
                <ul>
                    <li><a href="homepage.php">Dashboard</a></li>
                    <li><a href="register_donation.php">Donate</a></li>
                    <li><a href="register_claim.php">Claim</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="view_available_food.php">Available Food</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Welcome Section -->
    <section class="welcome">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Let's reduce food waste, one ingredient at a time. Here’s what you can do:</p>
        </div>
    </section>

    <!-- Dashboard Actions -->
    <section class="dashboard-actions">
        <div class="container">
            <div class="action-card">
                <h2>Make a Donation</h2>
                <p>Donate food to help others in need.</p>
                <a href="register_donation.php" class="btn btn-primary">Donate Now</a>
            </div>
            <div class="action-card">
                <h2>Claim Food</h2>
                <p>If you're in need, claim available food items.</p>
                <a href="register_claim.php" class="btn btn-secondary">Claim Food</a>
            </div>
            <div class="action-card">
                <h2>View Messages</h2>
                <p>See any messages about food donations or claims.</p>
                <a href="messages.php" class="btn btn-info">View Messages</a>
            </div>
        </div>
    </section>

    <!-- Recent Activity Section -->
    <section class="recent-activity">
        <div class="container">
            <h2>Recent Activity</h2>
            <p>Here’s a summary of your recent actions:</p>
            <ul class="activity-list">
                <li><strong>Donation:</strong> Donated 5 boxes of food to the community pantry.</li>
                <li><strong>Claim:</strong> Claimed food from a local donor in your area.</li>
                <li><strong>Message:</strong> Received a message about new available donations.</li>
            </ul>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="dashboard-footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> FoodShare. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
