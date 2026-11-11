<?php
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$sql = "SELECT * FROM items WHERE LOWER(purpose) = 'swap' ORDER BY id DESC";
$result = $conn->query($sql);
$items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();

// Project root on disk, resolved from this file so it works wherever the server is started.
$projectRoot = realpath(__DIR__ . '/..');
$placeholderImage = '../images/placeholder.svg';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Turns the imageURL stored in the DB into a browser path relative to pages/.
 * Uploaded items are stored as "uploads/<file>" (relative to project-folder/),
 * seeded items as "swap-items/<file>" (relative to images/).
 * Falls back to the placeholder if the file doesn't exist.
 */
function itemImageSrc(?string $imageURL, string $projectRoot, string $placeholder): string
{
    $relative = ltrim(str_replace('\\', '/', trim((string) $imageURL)), '/');
    if ($relative === '' || !preg_match('/\.(jpe?g|png|gif|webp|avif|svg)$/i', $relative)) {
        return $placeholder;
    }
    foreach (['project-folder', 'images'] as $baseDir) {
        $base = realpath("$projectRoot/$baseDir");
        $file = realpath("$projectRoot/$baseDir/$relative");
        // The file must exist and stay inside its base folder.
        if ($base && $file && is_file($file) && str_starts_with($file, $base . DIRECTORY_SEPARATOR)) {
            $fromRoot = substr($file, strlen($projectRoot) + 1);
            $segments = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $fromRoot));
            return '../' . implode('/', array_map('rawurlencode', $segments));
        }
    }
    return $placeholder;
}

/** Builds a wa.me link (Malaysian numbers starting with 0 get the 60 country code). */
function whatsappLink(?string $phone, string $itemName): ?string
{
    $digits = preg_replace('/\D+/', '', (string) $phone);
    if ($digits === '') {
        return null;
    }
    if (str_starts_with($digits, '0')) {
        $digits = '6' . $digits;
    }
    $message = "Hi! I saw your \"$itemName\" on EcoMarketplace Swap Offers. Is it still available for a swap?";
    return 'https://wa.me/' . $digits . '?text=' . rawurlencode($message);
}

function categoryLabel(?string $type): string
{
    $type = trim((string) $type);
    return $type === '' ? 'Others' : ucwords(strtolower($type));
}

// Category filter options, taken from the swap items themselves.
$categories = [];
foreach ($items as $item) {
    $label = categoryLabel($item['itemType'] ?? '');
    $categories[strtolower($label)] = $label;
}
ksort($categories);

$conditionClasses = ['like new' => 'cond-like-new', 'good' => 'cond-good', 'fair' => 'cond-fair'];
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
  <style>
    body {
      background: #f6f8f7;
      padding-top: 70px;
      font-family: 'Segoe UI', sans-serif;
    }

    [hidden] { display: none !important; }

    .swap-page {
      max-width: 1320px;
      margin: 0 auto;
      padding: 28px 16px 56px;
    }

    .swap-hero h1 {
      font-size: 2rem;
      font-weight: 700;
      color: #206444;
      margin-bottom: 4px;
    }

    .swap-hero p {
      color: #5b6b63;
      margin-bottom: 20px;
    }

    .swap-toolbar {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      background: #fff;
      border-radius: 12px;
      padding: 14px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
      margin-bottom: 12px;
    }

    .swap-toolbar .search-box { flex: 1 1 280px; }
    .swap-toolbar .category-box { flex: 0 1 240px; }

    .swap-toolbar .form-control:focus,
    .swap-toolbar .form-select:focus {
      border-color: #206444;
      box-shadow: 0 0 0 0.2rem rgba(32, 100, 68, 0.2);
    }

    .swap-toolbar .input-group-text {
      background: #fff;
      color: #206444;
    }

    .swap-count {
      color: #5b6b63;
      font-size: 0.9rem;
      margin-bottom: 18px;
    }

    /* 1 per row on mobile, 2 on tablets, 3 on laptops, 4 on wide screens */
    .swap-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
    }
    @media (min-width: 576px)  { .swap-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 992px)  { .swap-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1280px) { .swap-grid { grid-template-columns: repeat(4, 1fr); } }

    /* Not called .card: home_page.css styles .card for the homepage stack effect */
    .swap-card {
      display: flex;
      flex-direction: column;
      height: 100%;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .swap-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
    }

    .swap-card-img {
      position: relative;
      aspect-ratio: 4 / 3;
      background: #eef3ef;
    }

    .swap-card-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .swap-category {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(32, 100, 68, 0.92);
      color: #fff;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 999px;
    }

    .swap-card-body {
      display: flex;
      flex-direction: column;
      flex: 1;
      padding: 16px 18px 18px;
      gap: 10px;
    }

    .swap-card-name {
      font-size: 1.05rem;
      font-weight: 600;
      color: #1f2a24;
      margin: 0;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 2.6em;
      line-height: 1.3;
    }

    .swap-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      font-size: 0.85rem;
      color: #5b6b63;
    }

    .swap-owner {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .swap-condition {
      flex-shrink: 0;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 3px 9px;
      border-radius: 999px;
      background: #eef1ef;
      color: #4b5a55;
    }
    .cond-like-new { background: #dff3e6; color: #1b6b3f; }
    .cond-good     { background: #e3eefa; color: #285c95; }
    .cond-fair     { background: #fdf0d9; color: #8a5a12; }

    .swap-desc {
      font-size: 0.9rem;
      color: #3d4a43;
      line-height: 1.45;
      margin: 0;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 4.35em;
    }

    .swap-wants {
      background: #f0f7f2;
      border-left: 3px solid #206444;
      border-radius: 6px;
      padding: 8px 10px;
      font-size: 0.85rem;
      color: #2f3d35;
    }

    .swap-wants strong {
      display: block;
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #206444;
      margin-bottom: 2px;
    }

    .swap-wants span {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .swap-card-footer { margin-top: auto; }

    .btn-whatsapp {
      background-color: #25D366;
      border: none;
      color: #fff;
      font-weight: bold;
    }

    .btn-whatsapp:hover,
    .btn-whatsapp:focus {
      background-color: #1ebe59;
      color: #fff;
    }

    .swap-empty {
      text-align: center;
      color: #5b6b63;
      background: #fff;
      border-radius: 12px;
      padding: 48px 16px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .swap-empty i {
      font-size: 2.5rem;
      color: #9fb8a8;
      display: block;
      margin-bottom: 8px;
    }
  </style>
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
  </div>







</div>

<main class="swap-page">
  <section class="swap-hero">
    <h1>Swap Offers</h1>
    <p>Trade what you don't need for something you do. No money needed, just message the owner on WhatsApp.</p>
  </section>

  <form class="swap-toolbar" role="search" onsubmit="return false;">
    <div class="input-group search-box">
      <span class="input-group-text"><i class="bi bi-search"></i></span>
      <input type="search" id="swap-search" class="form-control" placeholder="Search items, wants, or owners…" aria-label="Search swap items">
    </div>
    <select id="swap-category" class="form-select category-box" aria-label="Filter by category">
      <option value="">All categories</option>
      <?php foreach ($categories as $key => $label) { ?>
        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
      <?php } ?>
    </select>
  </form>

  <p class="swap-count" id="swap-count" aria-live="polite">
    <?php echo count($items); ?> swap offer<?php echo count($items) === 1 ? '' : 's'; ?>
  </p>

  <?php if (empty($items)) { ?>
    <div class="swap-empty">
      <i class="bi bi-arrow-left-right"></i>
      No swap offers yet. Use <strong>Add item</strong> above to post the first one!
    </div>
  <?php } else { ?>
    <div class="swap-grid" id="swap-grid">
      <?php foreach ($items as $row) {
        $name = trim((string) ($row['itemName'] ?? '')) ?: 'Unnamed item';
        $category = categoryLabel($row['itemType'] ?? '');
        $condition = trim((string) ($row['itemCondition'] ?? ''));
        $description = trim((string) ($row['description'] ?? ''));
        $wants = trim((string) ($row['wantsInExchange'] ?? ''));
        $owner = trim((string) ($row['ownerName'] ?? ''));
        $imageSrc = itemImageSrc($row['imageURL'] ?? '', $projectRoot, $placeholderImage);
        $waLink = whatsappLink($row['phoneNumber'] ?? '', $name);
        $searchText = strtolower(implode(' ', [$name, $category, $condition, $description, $wants, $owner]));
      ?>
        <article class="swap-card" data-category="<?php echo e(strtolower($category)); ?>" data-search="<?php echo e($searchText); ?>">
          <div class="swap-card-img">
            <img src="<?php echo e($imageSrc); ?>" alt="<?php echo e($name); ?>" loading="lazy"
                 onerror="this.onerror=null; this.src='<?php echo e($placeholderImage); ?>';">
            <span class="swap-category"><?php echo e($category); ?></span>
          </div>

          <div class="swap-card-body">
            <h2 class="swap-card-name" title="<?php echo e($name); ?>"><?php echo e($name); ?></h2>

            <div class="swap-meta">
              <span class="swap-owner"><i class="bi bi-person-circle"></i> <?php echo e($owner !== '' ? $owner : 'EcoMarketplace user'); ?></span>
              <?php if ($condition !== '') { ?>
                <span class="swap-condition <?php echo $conditionClasses[strtolower($condition)] ?? ''; ?>"><?php echo e($condition); ?></span>
              <?php } ?>
            </div>

            <p class="swap-desc"><?php echo e($description !== '' ? $description : 'No description provided.'); ?></p>

            <div class="swap-wants">
              <strong><i class="bi bi-arrow-left-right"></i> Wants in exchange</strong>
              <span><?php echo e($wants !== '' ? $wants : 'Open to offers'); ?></span>
            </div>

            <div class="swap-card-footer">
              <?php if ($waLink) { ?>
                <a href="<?php echo e($waLink); ?>" target="_blank" rel="noopener" class="btn btn-whatsapp w-100">
                  <i class="bi bi-whatsapp"></i> WhatsApp Owner
                </a>
              <?php } else { ?>
                <button type="button" class="btn btn-secondary w-100" disabled>No contact number</button>
              <?php } ?>
            </div>
          </div>
        </article>
      <?php } ?>
    </div>

    <div class="swap-empty" id="swap-no-results" hidden>
      <i class="bi bi-search"></i>
      No swap offers match your search. Try a different keyword or category.
    </div>
  <?php } ?>
</main>

<script src="../script/general.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Search + category filter for the swap cards
  (function () {
    const grid = document.getElementById('swap-grid');
    if (!grid) return;

    const searchInput = document.getElementById('swap-search');
    const categorySelect = document.getElementById('swap-category');
    const countText = document.getElementById('swap-count');
    const noResults = document.getElementById('swap-no-results');
    const cards = Array.from(grid.querySelectorAll('.swap-card'));

    function applyFilters() {
      const words = searchInput.value.toLowerCase().trim().split(/\s+/).filter(Boolean);
      const category = categorySelect.value;
      let shown = 0;

      cards.forEach(function (card) {
        const matchesCategory = !category || card.dataset.category === category;
        const matchesSearch = words.every(function (w) { return card.dataset.search.includes(w); });
        const visible = matchesCategory && matchesSearch;
        card.hidden = !visible;
        if (visible) shown++;
      });

      const filtering = words.length > 0 || category !== '';
      countText.textContent = filtering
        ? 'Showing ' + shown + ' of ' + cards.length + ' swap offers'
        : cards.length + ' swap offer' + (cards.length === 1 ? '' : 's');
      noResults.hidden = shown !== 0;
      grid.hidden = shown === 0;
    }

    searchInput.addEventListener('input', applyFilters);
    categorySelect.addEventListener('change', applyFilters);
  })();
</script>
</body>
</html>
