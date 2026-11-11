<?php
require __DIR__ . '/../includes/items.php';

$items = fetchItems("LOWER(purpose) = 'swap'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Swap Offers - EcoMarketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../styles/home_page.css">
  <link rel="stylesheet" href="../styles/items.css">
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

<?php renderItemsSection($items, [
  'mode'              => 'swap',
  'title'             => 'Swap Offers',
  'subtitle'          => "Trade what you don't need for something you do. No money needed, just message the owner on WhatsApp.",
  'searchPlaceholder' => 'Search items, wants, or owners…',
  'categoryFilter'    => true,
  'noun'              => 'swap offer',
  'nounPlural'        => 'swap offers',
  'emptyIcon'         => 'bi-arrow-left-right',
  'emptyText'         => 'No swap offers yet. Use <strong>Add item</strong> above to post the first one!',
  'noResultsText'     => 'No swap offers match your search. Try a different keyword or category.',
]); ?>

<script src="../script/general.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script/items.js"></script>
</body>
</html>
