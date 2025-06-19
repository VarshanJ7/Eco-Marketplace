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

// ✅ Check if image was uploaded correctly
if (!isset($_FILES['itemImage']) || $_FILES['itemImage']['error'] !== 0) {
    die("❌ Image upload failed. Error code: " . $_FILES['itemImage']['error']);
}
echo "✅ Image upload received.<br>";

// ✅ Handle image upload
$imageName = "uploads/" . basename($_FILES['itemImage']['name']);
$imageTmp = $_FILES['itemImage']['tmp_name'];
$targetPath = __DIR__ . "/" . $imageName;

if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true);
    echo "✅ 'uploads' folder created.<br>";
} else {
    echo "✅ 'uploads' folder already exists.<br>";
}

if (!move_uploaded_file($imageTmp, $targetPath)) {
    die("❌ Failed to move uploaded file. Path: $targetPath<br>Check folder permissions.");
} else {
    echo "✅ File successfully moved to: $targetPath<br>";
}

// ✅ Collect form data
$userID = 1;
$itemName = $_POST['itemName'];
$price = $_POST['price'] ?? 0.00;
$purpose = strtolower($_POST['purpose']);
$itemType = strtolower($_POST['itemType']);
$phoneNumber = $_POST['phoneNumber'];

echo "✅ Collected form data:<br>";
echo "Item: $itemName<br>Price: $price<br>Purpose: $purpose<br>Type: $itemType<br>Phone: $phoneNumber<br>";

// ✅ SQL now includes itemName
$sql = "INSERT INTO items (itemName, userID, purpose, itemType, price, phoneNumber, imageURL)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("❌ SQL Prepare failed: " . $conn->error);
}

// ✅ 'sissdss' matches the types in the INSERT
$stmt->bind_param("sissdss", $itemName, $userID, $purpose, $itemType, $price, $phoneNumber, $imageName);

if ($stmt->execute()) {
    echo "✅ Item successfully added to the database!<br>";
} else {
    echo "❌ Insert failed: " . $stmt->error;
}

$conn->close();
?>
