<?php
/**
 * Shared helpers for the seed scripts in this folder (seed_swap_items.php, seed_sale_items.php).
 * Not meant to be run on its own.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Run the seed scripts from the command line, e.g. php database/seed_sale_items.php\n");
}

function seedConnect(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli("localhost", "root", "", "eco_marketplace");
    $conn->set_charset("utf8mb4");
    return $conn;
}

/** Adds the extra item detail columns if missing (all nullable, so other pages are unaffected). */
function ensureItemColumns(mysqli $conn): void
{
    $newColumns = [
        'description'     => 'TEXT NULL',
        'itemCondition'   => 'VARCHAR(30) NULL',   // "condition" is a reserved word in MySQL
        'wantsInExchange' => 'VARCHAR(255) NULL',
        'ownerName'       => 'VARCHAR(100) NULL',
    ];
    $existing = [];
    $res = $conn->query("SHOW COLUMNS FROM items");
    while ($col = $res->fetch_assoc()) {
        $existing[] = $col['Field'];
    }
    foreach ($newColumns as $name => $definition) {
        if (!in_array($name, $existing, true)) {
            $conn->query("ALTER TABLE items ADD COLUMN `$name` $definition");
            echo "Added column items.$name\n";
        }
    }
}

/**
 * Inserts each item unless an item with the same name and purpose already exists.
 * For existing items, only empty detail fields are filled in, and the image is only
 * replaced if the current one doesn't point at a real file.
 *
 * Each item: name, price, category, condition, description, wants (or null), owner, imageURL.
 */
function seedItems(mysqli $conn, string $purpose, array $items, string $phone = '0123456789'): void
{
    $projectRoot = dirname(__DIR__);

    $find = $conn->prepare("SELECT id, imageURL FROM items WHERE itemName = ? AND LOWER(purpose) = ? LIMIT 1");
    $fill = $conn->prepare(
        "UPDATE items SET
            description     = COALESCE(NULLIF(description, ''), ?),
            itemCondition   = COALESCE(NULLIF(itemCondition, ''), ?),
            wantsInExchange = COALESCE(NULLIF(wantsInExchange, ''), ?),
            ownerName       = COALESCE(NULLIF(ownerName, ''), ?)
         WHERE id = ?");
    $fixImage = $conn->prepare("UPDATE items SET imageURL = ? WHERE id = ?");
    $insert = $conn->prepare(
        "INSERT INTO items (itemName, price, purpose, itemType, phoneNumber, imageURL,
                            description, itemCondition, wantsInExchange, ownerName)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $added = $updated = 0;
    foreach ($items as $item) {
        [$name, $price, $category, $condition, $description, $wants, $owner, $imageURL] = $item;

        $find->bind_param("ss", $name, $purpose);
        $find->execute();
        $row = $find->get_result()->fetch_assoc();

        if ($row) {
            $id = (int) $row['id'];
            $fill->bind_param("ssssi", $description, $condition, $wants, $owner, $id);
            $fill->execute();

            $current = (string) $row['imageURL'];
            $exists = $current !== '' && (
                is_file("$projectRoot/project-folder/$current") || is_file("$projectRoot/images/$current"));
            if (!$exists) {
                $fixImage->bind_param("si", $imageURL, $id);
                $fixImage->execute();
            }
            $updated++;
        } else {
            $insert->bind_param("sdssssssss", $name, $price, $purpose, $category, $phone, $imageURL,
                $description, $condition, $wants, $owner);
            $insert->execute();
            $added++;
        }
    }

    echo "[$purpose] Inserted $added new item(s), checked $updated existing item(s).\n";
}
