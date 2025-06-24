<?php
session_start();

$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("❌ Database connection failed");
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $storedPass = $user['password'];

    // Check for plain password (user1) or SHA-256 hash (admin1/guest1)
    if ($storedPass === $password || $storedPass === hash('sha256', $password)) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = strtolower($user['role']);

        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'user':
                header("Location: ../pages/home_page.php");
                break;
            case 'guest':
                header("Location: guest_homepage.php");
                break;
            default:
                echo "❌ Unknown role";
        }
    } else {
        echo "❌ Wrong password";
    }
} else {
    echo "❌ No such user";
}

$conn->close();
?>