

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>




<!DOCTYPE html>
<html>
<head> 





  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous"> 

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="../styles/home_page.css">
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
            <li><a class="dropdown-item" href="others.php">Others</a></li>
          </ul>
        </div>
      
        <a href="swap.php"  style="cursor: pointer;  color: black; ">
          Swap offers
        </a>
        
    
    
        <p class="dropdown-toggle m-0" id="add-item-trigger" role="button" tabindex="0" aria-haspopup="dialog" style="cursor: pointer; margin: 0;">Add item</p>
          
       
    
    
    
      </div>
    
    
    
    <div class="right-section"> <a href="login.html">
      <img src="../images/profile.svg" class="profile-icon" alt="Profile">
    </a>
     </div>
    
    
    
    
    
    
    
    </div>

<div class="middle-page"   > 
  <div class="big-title">
    <p>
      <span style="color: green;">Shop Sustainably,</span><br>
      <span style="color: blue;">Student-to-Student</span>
    </p>
  </div>
</div>


<div class="middle2-page">
  <a href="kitchen.php" style="text-decoration: none; color: inherit;">
    <div class="image-card">
      <img src="../images/sink-pic.jpeg" alt="Kitchen">
      <div class="label">Kitchen</div>
    </div>
  </a>

  <a href="clothes.php" style="text-decoration: none; color: inherit;">
    <div class="image-card">
      <img src="../images/clothes.jpeg" alt="Clothes">
      <div class="label">Clothes</div>
    </div>
  </a>
</div>






<div class="stack-area">
  <div class="left">
    <div class="title2">Our Features</div>
    <div class="sub-title">
      Empowering students to <strong>swap, sell, and buy</strong> everyday items in a sustainable, budget-friendly way. 
      Join a movement that values sharing, eco-consciousness, and community support.
    </div>
  </div>
  <div class="right">
    <div class="card">
      <div class="sub">Swap Effortlessly</div>
      <div class="content">Exchange items without spending</div>
    </div>
    <div class="card">
      <div class="sub">Sell Sustainably</div>
      <div class="content">Give items a second life</div>
    </div>
    <div class="card">
      <div class="sub">Buy Affordably</div>
      <div class="content">Student-priced deals</div>
    </div>
    <div class="card">
      <div class="sub">Campus-Friendly</div>
      <div class="content">Built for student communities</div>
    </div>
  </div>
</div>


<!-- 🌱 Eco Footer Section -->
<div style="background-color: #2f5d42; color: white; padding: 40px 20px; font-family: 'Segoe UI', sans-serif;">
  <div class="container">
    <div class="row mb-4">
      <div class="col-md-6">
       
      </div>
      <div class="col-md-6 text-md-end text-center mt-4 mt-md-0">
        <img src="../images/earth.svg" style="width: 80px;" alt="Eco Logo">
        <h4 class="mt-2">EcoMarketplace</h4>
        <p>Powered by students for students 🌿</p>
      </div>
    </div>

    <hr style="border-color: rgba(255,255,255,0.2);">

    <div class="row text-center text-md-start">
      <div class="col-md-3">
        <h6 class="fw-bold">Explore</h6>
        <p>Browse Items</p>
        <p>Swap Offers</p>
        <p>Shop by Category</p>
      </div>
      <div class="col-md-3">
        <h6 class="fw-bold">About</h6>
        <p>Our Mission</p>
        <p>Student Partners</p>
        <p>Sustainability Goals</p>
      </div>
      <div class="col-md-3">
        <h6 class="fw-bold">Support</h6>
        <p>Contact Us</p>
        <p>FAQ</p>
        <p>Privacy Policy</p>
      </div>
     
    </div>

    <hr style="border-color: rgba(255,255,255,0.2);">
    <div class="text-center text-muted small">© 2025 EcoMarketplace. All rights reserved.</div>
  </div>
</div>













<script src="../script/general.js"></script>
<script src=../script/cards.js></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>


















</body>
</html>

