<?php
/**
 * Seeds the Swap Offers page with sample items for UPM students.
 *
 * Run from the project root:
 *     php database/seed_swap_items.php
 *
 * Safe to run more than once:
 *  - adds the extra columns (description, itemCondition, wantsInExchange, ownerName)
 *    to `items` only if they are missing (all nullable, so other pages are unaffected)
 *  - fills in details for the existing Toaster / Winter Jacket rows only where empty
 *  - inserts each sample item only if a swap item with that name doesn't exist yet
 *
 * Images live in images/swap-items/ and are stored in imageURL as "swap-items/<file>.svg"
 * (pages/swap.php resolves imageURL against project-folder/ first, then images/).
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Run this script from the command line: php database/seed_swap_items.php\n");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
$conn->set_charset("utf8mb4");

// 1. Add the new columns if they don't exist yet.
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

// 2. Swap items: [itemName, value RM, category, condition, description, wants, owner, image]
$phone = '0123456789'; // sample number, same as the existing rows
$items = [
    // Existing rows (details get filled in, images fixed)
    ['Toaster', 15.00, 'Kitchen', 'Good',
        '2-slice toaster with browning control. Works perfectly, but my new kolej doesn\'t allow toasters in the room.',
        'Table fan or desk lamp', 'Aina Sofea', 'toaster.svg'],
    ['Winter Jacket', 20.00, 'Clothes', 'Like New',
        'Size M padded winter jacket, worn only once during a mobility programme in Korea. Very warm, water-resistant.',
        'Sports shoes (UK 8) or a backpack', 'Hafiz Rahman', 'winter-jacket.svg'],

    // New items
    ['Engineering Mathematics Textbooks (2 books)', 60.00, 'Books & Stationery', 'Good',
        'Engineering Mathematics + Advanced Engineering Mathematics (Stroud). Some highlighting, no missing or torn pages.',
        'Programming or statistics textbooks', 'Nur Izzati', 'textbooks.svg'],
    ['Scientific Calculator (Casio fx-570EX)', 45.00, 'Electronics', 'Like New',
        'Exam-approved scientific calculator with hard cover. Battery replaced last semester.',
        'Wireless mouse or earphones', 'Lim Wei Jie', 'calculator.svg'],
    ['LED Desk Lamp', 25.00, 'Hostel & Room', 'Like New',
        'Adjustable arm, 3 brightness levels, USB powered. Great for late-night study sessions in the hostel.',
        'Extension plug or USB fan', 'Siti Aisyah', 'desk-lamp.svg'],
    ['Rice Cooker (1L)', 40.00, 'Kitchen', 'Good',
        'Compact 1L rice cooker, enough for 2–3 people. Non-stick inner pot still in good shape.',
        'Electric kettle or kitchen utensils', 'Muhammad Danial', 'rice-cooker.svg'],
    ['City Bicycle (26")', 150.00, 'Sports & Outdoors', 'Fair',
        'Handy for getting between faculties and to KTM Serdang. Tyres replaced recently, some surface rust on the frame.',
        'Monitor or anything useful for my room', 'Arjun Nair', 'bicycle.svg'],
    ['Running Shoes (UK 8)', 70.00, 'Sports & Outdoors', 'Good',
        'Lightweight running shoes, used for a few months of jogging at the UPM stadium track. Cleaned and ready to go.',
        'Badminton racket or gym bag', 'Kavitha Raj', 'sports-shoes.svg'],
    ['Electric Kettle (1.7L)', 30.00, 'Kitchen', 'Like New',
        'Stainless steel kettle with auto shut-off. Bought for the hostel but my roommate already has one.',
        'Rice cooker or table fan', 'Farah Nadia', 'kettle.svg'],
    ['Laptop Backpack', 50.00, 'Bags & Accessories', 'Good',
        'Fits a 15.6" laptop, padded compartment, water-resistant fabric. One small scuff on the bottom.',
        'Year 2 Computer Science textbooks', 'Tan Mei Ling', 'backpack.svg'],
    ['24" Full HD Monitor', 200.00, 'Electronics', 'Good',
        '24-inch 1080p monitor with HDMI cable. No dead pixels. Perfect as a second screen for coding or assignments.',
        'Bicycle or mechanical keyboard', 'Amirul Hakim', 'monitor.svg'],
    ['Hostel Storage Boxes (set of 3)', 35.00, 'Hostel & Room', 'Good',
        'Stackable 30L plastic boxes with lids. Moving out of kolej at the end of the semester and can\'t bring them home.',
        'Open to offers: snacks, stationery, anything useful', 'Nurul Huda', 'storage-box.svg'],
    ['Table Fan (12")', 35.00, 'Hostel & Room', 'Fair',
        '3 speed settings and oscillation both work. A bit noisy on the highest speed, fine on 1 and 2.',
        'Desk lamp or storage rack', 'Chong Kai Xuan', 'table-fan.svg'],
];

$find = $conn->prepare("SELECT id, imageURL FROM items WHERE itemName = ? AND LOWER(purpose) = 'swap' LIMIT 1");
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
     VALUES (?, ?, 'swap', ?, ?, ?, ?, ?, ?, ?)");

$projectRoot = dirname(__DIR__);
$added = $updated = 0;

foreach ($items as [$name, $price, $category, $condition, $description, $wants, $owner, $image]) {
    $imageURL = "swap-items/$image";

    $find->bind_param("s", $name);
    $find->execute();
    $row = $find->get_result()->fetch_assoc();

    if ($row) {
        $id = (int) $row['id'];
        $fill->bind_param("ssssi", $description, $condition, $wants, $owner, $id);
        $fill->execute();

        // Only replace the image if the current one doesn't point at a real file.
        $current = (string) $row['imageURL'];
        $exists = $current !== '' && (
            is_file("$projectRoot/project-folder/$current") || is_file("$projectRoot/images/$current"));
        if (!$exists) {
            $fixImage->bind_param("si", $imageURL, $id);
            $fixImage->execute();
        }
        $updated++;
    } else {
        $insert->bind_param("sdsssssss", $name, $price, $category, $phone, $imageURL,
            $description, $condition, $wants, $owner);
        $insert->execute();
        $added++;
    }
}

echo "Done. Inserted $added new swap item(s), updated $updated existing item(s).\n";
$conn->close();
