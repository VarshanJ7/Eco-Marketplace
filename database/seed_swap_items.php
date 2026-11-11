<?php
/**
 * Seeds the Swap Offers page with sample items for UPM students.
 *
 * Run from the project root:
 *     php database/seed_swap_items.php
 *
 * Safe to run more than once (see seedItems() in seed_lib.php):
 *  - adds the extra detail columns to `items` only if they are missing
 *  - fills in details for the existing Toaster / Winter Jacket rows only where empty
 *  - inserts each sample item only if a swap item with that name doesn't exist yet
 *
 * Images live in images/swap-items/ (imageURL "swap-items/<file>.svg", resolved by includes/items.php).
 */

require __DIR__ . '/seed_lib.php';

// [itemName, value RM, category, condition, description, wants in exchange, owner, imageURL]
$items = [
    // Existing rows (details get filled in, images fixed)
    ['Toaster', 15.00, 'Kitchen', 'Good',
        '2-slice toaster with browning control. Works perfectly, but my new kolej doesn\'t allow toasters in the room.',
        'Table fan or desk lamp', 'Aina Sofea', 'swap-items/toaster.svg'],
    ['Winter Jacket', 20.00, 'Clothes', 'Like New',
        'Size M padded winter jacket, worn only once during a mobility programme in Korea. Very warm, water-resistant.',
        'Sports shoes (UK 8) or a backpack', 'Hafiz Rahman', 'swap-items/winter-jacket.svg'],

    // New items
    ['Engineering Mathematics Textbooks (2 books)', 60.00, 'Books & Stationery', 'Good',
        'Engineering Mathematics + Advanced Engineering Mathematics (Stroud). Some highlighting, no missing or torn pages.',
        'Programming or statistics textbooks', 'Nur Izzati', 'swap-items/textbooks.svg'],
    ['Scientific Calculator (Casio fx-570EX)', 45.00, 'Electronics', 'Like New',
        'Exam-approved scientific calculator with hard cover. Battery replaced last semester.',
        'Wireless mouse or earphones', 'Lim Wei Jie', 'swap-items/calculator.svg'],
    ['LED Desk Lamp', 25.00, 'Hostel & Room', 'Like New',
        'Adjustable arm, 3 brightness levels, USB powered. Great for late-night study sessions in the hostel.',
        'Extension plug or USB fan', 'Siti Aisyah', 'swap-items/desk-lamp.svg'],
    ['Rice Cooker (1L)', 40.00, 'Kitchen', 'Good',
        'Compact 1L rice cooker, enough for 2–3 people. Non-stick inner pot still in good shape.',
        'Electric kettle or kitchen utensils', 'Muhammad Danial', 'swap-items/rice-cooker.svg'],
    ['City Bicycle (26")', 150.00, 'Sports & Outdoors', 'Fair',
        'Handy for getting between faculties and to KTM Serdang. Tyres replaced recently, some surface rust on the frame.',
        'Monitor or anything useful for my room', 'Arjun Nair', 'swap-items/bicycle.svg'],
    ['Running Shoes (UK 8)', 70.00, 'Sports & Outdoors', 'Good',
        'Lightweight running shoes, used for a few months of jogging at the UPM stadium track. Cleaned and ready to go.',
        'Badminton racket or gym bag', 'Kavitha Raj', 'swap-items/sports-shoes.svg'],
    ['Electric Kettle (1.7L)', 30.00, 'Kitchen', 'Like New',
        'Stainless steel kettle with auto shut-off. Bought for the hostel but my roommate already has one.',
        'Rice cooker or table fan', 'Farah Nadia', 'swap-items/kettle.svg'],
    ['Laptop Backpack', 50.00, 'Bags & Accessories', 'Good',
        'Fits a 15.6" laptop, padded compartment, water-resistant fabric. One small scuff on the bottom.',
        'Year 2 Computer Science textbooks', 'Tan Mei Ling', 'swap-items/backpack.svg'],
    ['24" Full HD Monitor', 200.00, 'Electronics', 'Good',
        '24-inch 1080p monitor with HDMI cable. No dead pixels. Perfect as a second screen for coding or assignments.',
        'Bicycle or mechanical keyboard', 'Amirul Hakim', 'swap-items/monitor.svg'],
    ['Hostel Storage Boxes (set of 3)', 35.00, 'Hostel & Room', 'Good',
        'Stackable 30L plastic boxes with lids. Moving out of kolej at the end of the semester and can\'t bring them home.',
        'Open to offers: snacks, stationery, anything useful', 'Nurul Huda', 'swap-items/storage-box.svg'],
    ['Table Fan (12")', 35.00, 'Hostel & Room', 'Fair',
        '3 speed settings and oscillation both work. A bit noisy on the highest speed, fine on 1 and 2.',
        'Desk lamp or storage rack', 'Chong Kai Xuan', 'swap-items/table-fan.svg'],
];

$conn = seedConnect();
ensureItemColumns($conn);
seedItems($conn, 'swap', $items);
$conn->close();
