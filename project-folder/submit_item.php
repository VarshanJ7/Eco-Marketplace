<?php
echo "🛠️ submit_item.php is being run<br>";

// ✅ Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Connect to the MySQL database
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected to MySQL successfully.<br>";

// 🔍 Check raw POST data
echo "<pre>";
var_dump($_POST);
var_dump($_FILES);
echo "</pre>";

// ✅ Skip image for now
$imageName = "uploads/test.jpg";

// ✅ Collect form data with fallbacks
$userID = 1;
$itemName = $_POST['itemName'] ?? 'EmptyItem';
$price = $_POST['price'] ?? 0.00;
$purpose = strtolower($_POST['purpose'] ?? 'Sell');
$itemType = strtolower($_POST['itemType'] ?? 'others');
$phoneNumber = $_POST['phoneNumber'] ?? '0000000000';

// 🔍 Show collected values
echo "<br>🧾 Collected values:<br>";
echo "Item Name: $itemName<br>";
echo "Price: $price<br>";
echo "Purpose: $purpose<br>";
echo "Type: $itemType<br>";
echo "Phone: $phoneNumber<br>";
echo "UserID: $userID<br>";
echo "Image: $imageName<br>";

// ✅ Build and run insert query
$sql = "INSERT INTO items (itemName, userID, purpose, itemType, price, phoneNumber, imageURL)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
}

$stmt->bind_param("sissdss", $itemName, $userID, $purpose, $itemType, $price, $phoneNumber, $imageName);

if ($stmt->execute()) {
    echo "✅ Item successfully added to the database!<br>";
} else {
    echo "❌ Execute failed: " . $stmt->error . "<br>";
    echo "⚠️ Try checking that userID $userID exists in the `users` table.";
}

$conn->close();
?>
