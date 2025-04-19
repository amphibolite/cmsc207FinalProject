<?php
require 'db.php'; // Uses $pdo

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $gender = $_POST["gender"];
    $birthdate = $_POST["birthdate"];
    $province = $_POST["province"];
    $city = $_POST["city"];
    $barangay = $_POST["barangay"];
    $street_address = $_POST["street_address"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if username exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo "Username already taken.";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO users (
            first_name, middle_name, last_name, gender, birthdate,
            province, city, barangay, street_address,
            username, password, apply_dropoff
        ) VALUES (
            :first_name, :middle_name, :last_name, :gender, :birthdate,
            :province, :city, :barangay, :street_address,
            :username, :password, false
        )
    ");

    $stmt->execute([
        'first_name' => $first_name,
        'middle_name' => $middle_name,
        'last_name' => $last_name,
        'gender' => $gender,
        'birthdate' => $birthdate,
        'province' => $province,
        'city' => $city,
        'barangay' => $barangay,
        'street_address' => $street_address,
        'username' => $username,
        'password' => $hashedPassword
    ]);

    echo "Registration successful! <a href='login.html'>Login here</a>";
}
?>
