<?php
/**
 * Shared helpers for the item listing pages (swap.php, kitchen.php, clothes.php, others.php):
 * DB loading, image path resolution, WhatsApp links and the card / grid markup.
 * Styles live in styles/items.css, search/filter/sort in script/items.js.
 */

const ITEM_PLACEHOLDER_IMAGE = '../images/placeholder.svg';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Loads items matching a fixed (non user-supplied) WHERE clause, newest first. */
function fetchItems(string $where): array
{
    $conn = new mysqli("localhost", "root", "", "eco_marketplace");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    $result = $conn->query("SELECT * FROM items WHERE $where ORDER BY id DESC");
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $items;
}

/**
 * Turns the imageURL stored in the DB into a browser path relative to pages/.
 * Uploaded items are stored as "uploads/<file>" (relative to project-folder/),
 * seeded items as "swap-items/<file>" or "sale-items/<file>" (relative to images/).
 * Falls back to the placeholder if the file doesn't exist.
 */
function itemImageSrc(?string $imageURL): string
{
    $projectRoot = realpath(__DIR__ . '/..');
    $relative = ltrim(str_replace('\\', '/', trim((string) $imageURL)), '/');
    if ($relative === '' || !preg_match('/\.(jpe?g|png|gif|webp|avif|svg)$/i', $relative)) {
        return ITEM_PLACEHOLDER_IMAGE;
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
    return ITEM_PLACEHOLDER_IMAGE;
}

/** Builds a wa.me link (Malaysian numbers starting with 0 get the 60 country code). */
function whatsappLink(?string $phone, string $message): ?string
{
    $digits = preg_replace('/\D+/', '', (string) $phone);
    if ($digits === '') {
        return null;
    }
    if (str_starts_with($digits, '0')) {
        $digits = '6' . $digits;
    }
    return 'https://wa.me/' . $digits . '?text=' . rawurlencode($message);
}

function categoryLabel(?string $type): string
{
    $type = trim((string) $type);
    return $type === '' ? 'Others' : ucwords(strtolower($type));
}

function conditionClass(string $condition): string
{
    $classes = ['like new' => 'cond-like-new', 'good' => 'cond-good', 'fair' => 'cond-fair'];
    return $classes[strtolower($condition)] ?? '';
}

function formatPrice($price): string
{
    return 'RM ' . number_format((float) $price, 2);
}

function countLabel(int $count, string $noun, string $nounPlural): string
{
    return $count . ' ' . ($count === 1 ? $noun : $nounPlural);
}

/** One item card. $mode is 'swap' (wants-in-exchange box) or 'sale' (price). */
function renderItemCard(array $row, string $mode): void
{
    $isSwap = $mode === 'swap';
    $name = trim((string) ($row['itemName'] ?? '')) ?: 'Unnamed item';
    $category = categoryLabel($row['itemType'] ?? '');
    $condition = trim((string) ($row['itemCondition'] ?? ''));
    $description = trim((string) ($row['description'] ?? ''));
    $wants = trim((string) ($row['wantsInExchange'] ?? ''));
    $owner = trim((string) ($row['ownerName'] ?? ''));
    $price = (float) ($row['price'] ?? 0);
    $imageSrc = itemImageSrc($row['imageURL'] ?? '');
    $message = $isSwap
        ? "Hi! I saw your \"$name\" on EcoMarketplace Swap Offers. Is it still available for a swap?"
        : "Hi! I saw your \"$name\" for " . formatPrice($price) . " on EcoMarketplace. Is it still available?";
    $waLink = whatsappLink($row['phoneNumber'] ?? '', $message);
    $searchText = strtolower(implode(' ', [$name, $category, $condition, $description, $isSwap ? $wants : '', $owner]));
    $conditionBadge = $condition === '' ? '' :
        '<span class="item-condition ' . conditionClass($condition) . '">' . e($condition) . '</span>';
    ?>
    <article class="item-card" data-id="<?php echo (int) ($row['id'] ?? 0); ?>" data-price="<?php echo e($price); ?>"
             data-category="<?php echo e(strtolower($category)); ?>" data-search="<?php echo e($searchText); ?>">
      <div class="item-card-img">
        <img src="<?php echo e($imageSrc); ?>" alt="<?php echo e($name); ?>" loading="lazy"
             onerror="this.onerror=null; this.src='<?php echo e(ITEM_PLACEHOLDER_IMAGE); ?>';">
        <?php if ($isSwap) { ?><span class="item-category"><?php echo e($category); ?></span><?php } ?>
      </div>

      <div class="item-card-body">
        <h2 class="item-card-name" title="<?php echo e($name); ?>"><?php echo e($name); ?></h2>

        <?php if (!$isSwap) { ?>
          <div class="item-price-row">
            <span class="item-price"><?php echo e(formatPrice($price)); ?></span>
            <?php echo $conditionBadge; ?>
          </div>
        <?php } ?>

        <div class="item-meta">
          <span class="item-owner"><i class="bi bi-person-circle"></i> <?php echo e($owner !== '' ? $owner : 'EcoMarketplace user'); ?></span>
          <?php if ($isSwap) { echo $conditionBadge; } ?>
        </div>

        <p class="item-desc"><?php echo e($description !== '' ? $description : 'No description provided.'); ?></p>

        <?php if ($isSwap) { ?>
          <div class="item-wants">
            <strong><i class="bi bi-arrow-left-right"></i> Wants in exchange</strong>
            <span><?php echo e($wants !== '' ? $wants : 'Open to offers'); ?></span>
          </div>
        <?php } ?>

        <div class="item-card-footer">
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
    <?php
}

/**
 * The whole listing section: heading, toolbar, count, grid and empty states.
 * Options: title, subtitle, mode ('swap'|'sale'), noun, nounPlural, emptyIcon, emptyText,
 *          categoryFilter (bool), sort (bool).
 */
function renderItemsSection(array $items, array $opt): void
{
    $total = count($items);
    $categories = [];
    if (!empty($opt['categoryFilter'])) {
        foreach ($items as $item) {
            $label = categoryLabel($item['itemType'] ?? '');
            $categories[strtolower($label)] = $label;
        }
        ksort($categories);
    }
    ?>
<main class="items-page">
  <section class="items-hero">
    <h1><?php echo e($opt['title']); ?></h1>
    <p><?php echo e($opt['subtitle']); ?></p>
  </section>

  <form class="items-toolbar" role="search" onsubmit="return false;">
    <div class="input-group search-box">
      <span class="input-group-text"><i class="bi bi-search"></i></span>
      <input type="search" id="item-search" class="form-control" placeholder="<?php echo e($opt['searchPlaceholder'] ?? 'Search items…'); ?>" aria-label="Search items">
    </div>
    <?php if (!empty($opt['categoryFilter'])) { ?>
      <select id="item-category" class="form-select toolbar-select" aria-label="Filter by category">
        <option value="">All categories</option>
        <?php foreach ($categories as $key => $label) { ?>
          <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
        <?php } ?>
      </select>
    <?php } ?>
    <?php if (!empty($opt['sort'])) { ?>
      <select id="item-sort" class="form-select toolbar-select" aria-label="Sort items">
        <option value="newest">Sort: Newest</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
      </select>
    <?php } ?>
  </form>

  <p class="items-count" id="item-count" aria-live="polite"><?php echo e(countLabel($total, $opt['noun'], $opt['nounPlural'])); ?></p>

  <?php if ($total === 0) { ?>
    <div class="items-empty">
      <i class="bi <?php echo e($opt['emptyIcon'] ?? 'bi-box-seam'); ?>"></i>
      <?php echo $opt['emptyText']; /* trusted HTML from the page */ ?>
    </div>
  <?php } else { ?>
    <div class="items-grid" id="item-grid" data-noun="<?php echo e($opt['noun']); ?>" data-noun-plural="<?php echo e($opt['nounPlural']); ?>">
      <?php foreach ($items as $row) { renderItemCard($row, $opt['mode']); } ?>
    </div>

    <div class="items-empty" id="item-no-results" hidden>
      <i class="bi bi-search"></i>
      <?php echo e($opt['noResultsText'] ?? 'No items match your search. Try a different keyword.'); ?>
    </div>
  <?php } ?>
</main>
    <?php
}
