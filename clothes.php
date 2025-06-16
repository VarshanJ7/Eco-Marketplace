<?php
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM items WHERE itemType = 'clothes'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html> 
<head> 
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="../styles/home_page.css">
</head>

<body> 
  <div class="header"> 
    <div class="left-section">  
      <a href="home_page.html" class="title">ECOMARKETPLACE</a>
    </div>

    <div class="middle-section"> 
      <div class="dropdown">
        <p class="dropdown-toggle m-0" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">Browse items</p>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="kitchen.html">Kitchen</a></li>
          <li><a class="dropdown-item" href="clothes.php">Clothes</a></li>
          <li><a class="dropdown-item" href="#">Others</a></li>
        </ul>
      </div>

      <a href="swap.html" style="cursor: pointer; color: black;">Swap offers</a>

      <div class="dropdown">
        <p class="dropdown-toggle m-0" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">Add item</p>
        <ul class="dropdown-menu p-4" style="min-width: 300px;">
          <form action="../submit_item.php" method="POST" enctype="multipart/form-data">
            <div class="mb-2">
              <label class="form-label">Item Name</label>
              <input type="text" class="form-control" name="itemName" placeholder="Enter item name" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Item Value (RM)</label>
              <input type="number" class="form-control" name="price" placeholder="Enter value">
            </div>
            <div class="mb-2">
              <label class="form-label">Purpose</label>
              <select class="form-select" name="purpose">
                <option value="Swap">Swap</option>
                <option value="Sell">Sell</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Type</label>
              <select class="form-select" name="itemType">
                <option value="Kitchen">Kitchen</option>
                <option value="Clothes">Clothes</option>
                <option value="Others">Others</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">WhatsApp Number</label>
              <input type="tel" class="form-control" name="phoneNumber" placeholder="e.g. 0123456789" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Upload Item Image</label>
              <input type="file" class="form-control" accept="image/*" name="itemImage" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Submit</button>
          </form>
        </ul>
      </div>
    </div>

    <div class="right-section">
      <a href="login.html"><img src="../images/profile.svg" class="profile-icon" alt="Profile"></a>
      <img src="../images/cart.svg" class="cart-icon">
    </div>
  </div>

  <!-- Clothes Grid from Database -->
  <div class="clothes-grid" id="clothes-list">
    <?php while ($row = $result->fetch_assoc()) { ?>
      <div class="main-container">
        <div class="img-container">
          <img src="../uploads/<?php echo $row['imageURL']; ?>" alt="item">
        </div>
        <div class="desc-container">
          <p>RM <?php echo $row['price']; ?></p>
          <p>Purpose: <?php echo ucfirst($row['purpose']); ?></p>
          <p>Phone: <?php echo $row['phoneNumber']; ?></p>
        </div>
        <div class="button-container text-center">
          <a href="https://wa.me/6<?php echo $row['phoneNumber']; ?>" target="_blank" class="btn btn-success">Chat on WhatsApp</a>
        </div>
      </div>
    <?php } ?>
  </div>

<style>
body {
  margin: 0;
  padding-top: 70px;
  background: white;
  font-family: 'Segoe UI', sans-serif;
}

.clothes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 30px;
  padding: 40px;
}

.main-container {
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.main-container:hover {
  transform: translateY(-5px);
}

.img-container img {
  width: 100%;
  height: 500px;
  object-fit: contain;
}

.desc-container {
  padding: 15px 20px 0;
  font-size: 16px;
  font-weight: 500;
  color: #2f2f2f;
  text-align: center;
  line-height: 1.5;
}

.button-container {
  padding: 15px 20px 20px;
}

.button-container .btn {
  background-color: #25D366;
  border: none;
  font-weight: bold;
}

.button-container .btn:hover {
  background-color: #1ebe59;
}
</style>


</body>
</html>

<?php $conn->close(); ?>
