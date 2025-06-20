<?php
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM items WHERE LOWER(itemType) = 'clothes' AND LOWER(purpose) = 'sell'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../styles/home_page.css">
  <link rel="stylesheet" href="../styles/clothes.css">
</head>
<body>

<div class="header"> 

 
<div class="left-section">  <a href = "home_page.php" class="title">ECOMARKETPLACE </a>  </div>


<div class="middle-section"> 
    <div class="dropdown">
      <p class="dropdown-toggle m-0" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; margin: 0;">
        Browse items
      </p>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="kitchen.php">Kitchen</a></li>
        <li><a class="dropdown-item" href="clothes.php">Clothes</a></li>
        <li><a class="dropdown-item" href="#">Others</a></li>
      </ul>
    </div>
  
    <a href="swap.php"  style="cursor: pointer;  color: black; ">
      Swap offers
    </a>
    


    <div class="dropdown">
        <p class="dropdown-toggle m-0" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; margin: 0;">Add item</p>
        <ul class="dropdown-menu p-4" style="min-width: 300px;">
          <form id="add-item-form"  action="../project-folder/submit_item.php" method="POST" enctype="multipart/form-data">   <!-- tells form send data to php file submit_item.php via -->
            <div class="mb-2">
              <label class="form-label">Item Name</label>
              <input type="text" class="form-control" id="item-name" name="itemName"  placeholder="Enter item name"> <!-- PHP can only access values sent via name="itemName", not id -->
            </div>
            <div class="mb-2">
              <label class="form-label">Item Value (RM)</label>
              <input type="number" class="form-control" id="item-price" 
              name="price" step="0.01"   placeholder="Enter value">
            </div>
            <div class="mb-2">
              <label class="form-label" >Purpose</label>
              <select class="form-select" id="item-purpose" name="purpose">
                <option value="Swap">Swap</option>
                <option value="Sell">Sell</option>
              </select>
            </div>
            <div class="mb-2">
                <label class="form-label" >Type</label>
                <select class="form-select" id="item-type"  name="itemType" >
                  <option value="Kitchen">Kitchen</option>
                  <option value="Clothes">Clothes</option>
                  <option value="Others">Others</option>
                </select>
              </div>
            <div class="mb-2">
              <label class="form-label">WhatsApp Number</label>
              <input type="tel" class="form-control"  id="item-number"  name="phoneNumber" placeholder="e.g. 0123456789">
            </div>
            <div class="mb-3">
              <label class="form-label">Upload Item Image</label>
              <input type="file" class="form-control" accept="image/*" name="itemImage"  id="item-image" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Submit</button>
          </form>
        </ul>
      </div>
      
   



  </div>



<div class="right-section"> <a href="login.html">
  <img src="../images/profile.svg" class="profile-icon" alt="Profile">
</a>
 <img src="../images/cart.svg" class ="cart-icon"> </div>







</div>

<!--  CLOTHES DISPLAY SECTION -->
<div class="clothes-grid" id="clothes-list">
  <?php while ($row = $result->fetch_assoc()) { ?>
    <div class="main-container">
      <div class="img-container">
      <img src="../project-folder/<?php echo htmlspecialchars($row['imageURL']); ?>" alt="Item Image">

      </div>
      <div class="desc-container">
        <?php echo htmlspecialchars($row['itemName']); ?><br>
        <?php echo number_format($row['price'], 2); ?>
      </div>
      <div class="button-container">
        <a href="https://wa.me/<?php echo htmlspecialchars($row['phoneNumber']); ?>" target="_blank" class="btn btn-success w-100">
          <i class="bi bi-whatsapp"></i> WhatsApp Seller
        </a>
      </div>
    </div>
  <?php } ?>
</div>

<script src="../script/general.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
