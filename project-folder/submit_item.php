<?php
/**
 * Handles the "Add item" form (project-folder/add_item_form.php).
 *
 * - script/general.js submits it with fetch (header "X-Requested-With: fetch") and gets JSON back:
 *     { "ok": true,  "redirect": "../pages/kitchen.php?added=42" }
 *     { "ok": false, "errors": { "fieldName": "message", "form": "general message" } }
 * - A normal (non-JS) form post is redirected on success, or shown a plain error page.
 *
 * Keep the limits below in sync with the form and the checks in script/general.js.
 */
session_start();

const MAX_IMAGE_BYTES = 5 * 1024 * 1024;
const ALLOWED_IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
const ITEM_TYPES = ['kitchen' => 'Kitchen', 'clothes' => 'Clothes', 'others' => 'Others'];
const CONDITIONS = ['like new' => 'Like New', 'good' => 'Good', 'fair' => 'Fair'];

$isFetch = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

function respond(bool $ok, array $data, bool $isFetch): void
{
    if ($isFetch) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok] + $data);
        exit;
    }
    if ($ok) {
        header('Location: ' . $data['redirect']);
        exit;
    }
    // Fallback page when JavaScript is off. Going back keeps what was typed in most browsers.
    http_response_code(422);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<title>Item not added - EcoMarketplace</title>'
        . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"></head>'
        . '<body class="p-4"><div class="alert alert-danger" style="max-width:520px"><strong>Your item was not added:</strong><ul class="mb-2">';
    foreach ($data['errors'] as $message) {
        echo '<li>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul><a href="javascript:history.back()" class="btn btn-success btn-sm">Go back and fix it</a></div></body></html>';
    exit;
}

function field(string $name): string
{
    return trim((string) ($_POST[$name] ?? ''));
}

/** Malaysian mobile number -> "01X-XXXXXXX" (or "011-XXXXXXXX"), or null if invalid. */
function normalizeMalaysianPhone(string $input): ?string
{
    $digits = preg_replace('/\D+/', '', $input);
    if (str_starts_with($digits, '60')) {
        $digits = substr($digits, 1);          // +60 12... / 6012... -> 012...
    }
    if (!preg_match('/^01(1\d{8}|[02-9]\d{7})$/', $digits)) {
        return null;
    }
    return substr($digits, 0, 3) . '-' . substr($digits, 3);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/home_page.php');
    exit;
}

// Upload bigger than post_max_size: PHP silently drops the whole POST body.
if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    respond(false, ['errors' => ['itemImage' => 'That image is too large. The maximum size is 5MB.']], $isFetch);
}

// ---------- Validate text fields ----------
$errors = [];

$itemName = field('itemName');
if ($itemName === '') {
    $errors['itemName'] = 'Please enter the item name.';
} elseif (mb_strlen($itemName) > 80) {
    $errors['itemName'] = 'Item name must be 80 characters or less.';
}

$purpose = strtolower(field('purpose'));
if (!in_array($purpose, ['sell', 'swap'], true)) {
    $errors['purpose'] = 'Choose Sell or Swap.';
}

$itemType = ITEM_TYPES[strtolower(field('itemType'))] ?? null;
if ($itemType === null) {
    $errors['itemType'] = 'Choose Kitchen, Clothes or Others.';
}

$condition = CONDITIONS[strtolower(field('itemCondition'))] ?? null;
if ($condition === null) {
    $errors['itemCondition'] = 'Choose the item condition.';
}

$price = null;
$wants = null;
if ($purpose === 'sell') {
    $priceInput = field('price');
    if ($priceInput === '' || !is_numeric($priceInput) || (float) $priceInput <= 0) {
        $errors['price'] = 'Enter a price greater than RM 0.';
    } elseif ((float) $priceInput > 99999.99) {
        $errors['price'] = 'Price must be below RM 100,000.';
    } else {
        $price = round((float) $priceInput, 2);
    }
} elseif ($purpose === 'swap') {
    $wants = field('wantsInExchange');
    if ($wants === '') {
        $errors['wantsInExchange'] = 'Tell others what you want in exchange.';
    } elseif (mb_strlen($wants) > 120) {
        $errors['wantsInExchange'] = 'Keep this to 120 characters or less.';
    }
}

$description = field('description');
if ($description === '') {
    $errors['description'] = 'Please add a short description.';
} elseif (mb_strlen($description) > 200) {
    $errors['description'] = 'Description must be 200 characters or less.';
}

$ownerName = field('ownerName');
if ($ownerName === '' && !empty($_SESSION['username'])) {
    $ownerName = trim((string) $_SESSION['username']);
}
if ($ownerName === '') {
    $errors['ownerName'] = 'Please enter your name.';
} elseif (mb_strlen($ownerName) > 60) {
    $errors['ownerName'] = 'Name must be 60 characters or less.';
}

$phone = normalizeMalaysianPhone(field('phoneNumber'));
if ($phone === null) {
    $errors['phoneNumber'] = 'Enter a Malaysian mobile number, e.g. 012-3456789.';
}

// ---------- Validate the image (optional) ----------
$upload = $_FILES['itemImage'] ?? null;
$uploadError = $upload['error'] ?? UPLOAD_ERR_NO_FILE;
$imageExtension = null;

if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE) {
    $errors['itemImage'] = 'That image is too large. The maximum size is 5MB.';
} elseif ($uploadError !== UPLOAD_ERR_OK && $uploadError !== UPLOAD_ERR_NO_FILE) {
    $errors['itemImage'] = 'The image could not be uploaded. Please try again.';
} elseif ($uploadError === UPLOAD_ERR_OK) {
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
    if ($upload['size'] > MAX_IMAGE_BYTES) {
        $errors['itemImage'] = 'That image is too large. The maximum size is 5MB.';
    } elseif (!isset(ALLOWED_IMAGE_TYPES[$mime]) || @getimagesize($upload['tmp_name']) === false) {
        $errors['itemImage'] = 'Only JPG, PNG or WEBP images are allowed.';
    } else {
        $imageExtension = ALLOWED_IMAGE_TYPES[$mime]; // from the real file type, not the file name
    }
}

if ($errors) {
    respond(false, ['errors' => $errors], $isFetch);
}

// ---------- Save the image ----------
$imageURL = null; // no image -> the cards show images/placeholder.svg
$savedImagePath = null;
if ($imageExtension !== null) {
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileName = date('Ymd-His') . '-' . bin2hex(random_bytes(6)) . '.' . $imageExtension;
    $savedImagePath = "$uploadDir/$fileName";
    if (!move_uploaded_file($upload['tmp_name'], $savedImagePath)) {
        respond(false, ['errors' => ['itemImage' => 'The image could not be saved. Please try again.']], $isFetch);
    }
    $imageURL = "uploads/$fileName"; // relative to project-folder/, where includes/items.php looks
}

// ---------- Insert ----------
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli("localhost", "root", "", "eco_marketplace");
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare(
        "INSERT INTO items (itemName, price, purpose, itemType, phoneNumber, imageURL,
                            description, itemCondition, wantsInExchange, ownerName)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssssssss", $itemName, $price, $purpose, $itemType, $phone, $imageURL,
        $description, $condition, $wants, $ownerName);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $conn->close();
} catch (mysqli_sql_exception $e) {
    if ($savedImagePath && is_file($savedImagePath)) {
        unlink($savedImagePath);
    }
    error_log('submit_item.php: ' . $e->getMessage());
    respond(false, ['errors' => ['form' => 'Something went wrong while saving your item. Please try again.']], $isFetch);
}

// ---------- Send the user to the page where the item now appears ----------
$page = $purpose === 'swap' ? 'swap.php' : strtolower($itemType) . '.php'; // kitchen / clothes / others
respond(true, ['redirect' => "../pages/$page?added=$newId"], $isFetch);
