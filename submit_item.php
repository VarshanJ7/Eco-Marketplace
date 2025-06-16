<?php
// connect to the mysql database
$conn = new mysqli("localhost", "root", "", "eco_marketplace");

// Checks if the connection failed. If so, the script stops and prints the error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  Handle image upload
$imageName = $_FILES['itemImage']['name'];
$imageTmp = $_FILES['itemImage']['tmp_name'];
$targetPath = "uploads/" . basename($imageName);

// Create uploads folder if not exists
if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
}

// Move uploaded image to /uploads folder
move_uploaded_file($imageTmp, $targetPath);

//  Collect form data 
$userID = 1; // Hardcoded user for now  
$itemName = $_POST['itemName'];
$price = $_POST['price'] ?? 0.00; // price might be optional for swap
$purpose = strtolower($_POST['purpose']);
$itemType = strtolower($_POST['itemType']);
$phoneNumber = $_POST['phoneNumber'];

//  Insert data into 'items' table
$sql = "INSERT INTO items (userID, purpose, itemType, price, phoneNumber, imageURL)
        VALUES (?, ?, ?, ?, ?, ?)";

        // Prepares and binds the actual values into the SQL
$stmt = $conn->prepare($sql);
$stmt->bind_param("issdss", $userID, $purpose, $itemType, $price, $phoneNumber, $imageName);


//If success: shows a popup + redirects to homepage
// If fails: shows error message
if ($stmt->execute()) {
    echo "<script>alert('Item successfully added!'); window.location.href='pages/home_page.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

//  Close connection
$conn->close();
?>
