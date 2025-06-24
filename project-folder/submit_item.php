<?php
// Debug message
echo "🛠️ submit_item.php is being run<br>";

// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

//   Connect to the MySQL database
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected to MySQL successfully.<br>";

//  Handle uploaded image
if (isset($_FILES['itemImage']) && $_FILES['itemImage']['error'] === 0) {
    $imageName = "uploads/" . basename($_FILES['itemImage']['name']);
    $targetPath = __DIR__ . "/" . $imageName;
    
    // Move uploaded image to "uploads/" folder
    if (move_uploaded_file($_FILES['itemImage']['tmp_name'], $targetPath)) {
        echo "✅ Image uploaded to $imageName<br>";
    } else {
        die("❌ Failed to move uploaded image. Check folder permissions.");
    }
} else {
    die("❌ Image not uploaded properly.");
}

// . Collect form data from the POST request
$userID = 1; // Temporary static userID (in real case, get from session)
$itemName = $_POST['itemName'] ?? 'Unnamed';
$price = $_POST['price'] ?? 0.00;
$purpose = strtolower($_POST['purpose'] ?? 'sell');
$itemType = strtolower($_POST['itemType'] ?? 'others');
$phoneNumber = $_POST['phoneNumber'] ?? '0000000000';

//  Prepare SQL INSERT statement using placeholders
$sql = "INSERT INTO items (itemName, userID, purpose, itemType, price, phoneNumber, imageURL)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

//  Prepare and bind parameters to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param("sissdss", $itemName, $userID, $purpose, $itemType, $price, $phoneNumber, $imageName);

//  Execute and check for success
if ($stmt->execute()) {
    echo "✅ Item added to DB successfully.<br>";
    echo "<script>window.location.href = '../pages/home_page.php';</script>";
} else {
    echo "❌ Insert failed: " . $stmt->error;
}

// ✅ 7. Close the database connection
$conn->close();
?>
