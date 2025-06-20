<?php
echo "🛠️ submit_item.php is being run<br>";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Connect to database
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected to MySQL successfully.<br>";

// ✅ Handle uploaded image
if (isset($_FILES['itemImage']) && $_FILES['itemImage']['error'] === 0) {
    $imageName = "uploads/" . basename($_FILES['itemImage']['name']);
    $targetPath = __DIR__ . "/" . $imageName;
    if (move_uploaded_file($_FILES['itemImage']['tmp_name'], $targetPath)) {
        echo "✅ Image uploaded to $imageName<br>";
    } else {
        die("❌ Failed to move uploaded image. Check folder permissions.");
    }
} else {
    die("❌ Image not uploaded properly.");
}

// ✅ Collect form data
$userID = 1;
$itemName = $_POST['itemName'] ?? 'Unnamed';
$price = $_POST['price'] ?? 0.00;
$purpose = strtolower($_POST['purpose'] ?? 'sell');
$itemType = strtolower($_POST['itemType'] ?? 'others');
$phoneNumber = $_POST['phoneNumber'] ?? '0000000000';

// ✅ Insert into DB
$sql = "INSERT INTO items (itemName, userID, purpose, itemType, price, phoneNumber, imageURL)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sissdss", $itemName, $userID, $purpose, $itemType, $price, $phoneNumber, $imageName);

if ($stmt->execute()) {
    echo "✅ Item added to DB successfully.<br>";
    echo "<script>window.location.href = '../pages/home_page.php';</script>";
} else {
    echo "❌ Insert failed: " . $stmt->error;
}

$conn->close();
?>
