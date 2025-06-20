<?php
session_start(); //  Start a session to store user info after login

//  Connect to the MySQL database
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("❌ Database connection failed");
}

// ✅ Get the username and password entered in the login form via name attributes
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare a SQL statement to find the user with that username
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username); // "s" = string
$stmt->execute();  

//  Get the result of the query
$result = $stmt->get_result();

//  If a user with that username exists
if ($user = $result->fetch_assoc()) {

    //  Compare the hashed password from DB with the entered one (also hashed)
    if (hash('sha256', $password) === $user['password']) {

        //  Save username and role in the session
        $_SESSION['username'] = $username;
        $_SESSION['role'] = strtolower($user['role']); //  convert role to lowercase


        //  Redirect based on the role
        if ($user['role'] === 'Admin') {
            header("Location: admin_dashboard.php"); // Admin goes here
        } elseif ($user['role'] === 'User') {
            header("Location: user_homepage.php"); // User goes here
        } else {
            header("Location: guest_homepage.php"); // Guest goes here
        }

    } else {
        echo "❌ Wrong password"; // Entered password is wrong
    }

} else {
    echo "❌ No such user"; // Username not found in DB
}

$conn->close(); // ✅ Close the DB connection
?>
