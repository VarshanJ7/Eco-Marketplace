<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "eco_marketplace");

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Get user input from the form
$username = $_POST['username'];
$password = $_POST['password'];

// Hash the password using SHA-256
$hashed_password = hash('sha256', $password);

// Check if the username already exists
//safety measure to prevent SQL injection
$check = $conn->prepare("SELECT * FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "❌ Username already taken.";
} else {
    // Insert new user with role = User
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'User')");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: ../pages/login.html");
        exit();
    } else {
        echo "❌ Failed to register. Try again.";
    }
}

$conn->close();
?>
