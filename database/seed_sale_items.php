<?php
/**
 * Seeds the Kitchen, Clothes and Others pages with sample items for sale (purpose = 'sell').
 *
 * Run from the project root:
 *     php database/seed_sale_items.php
 *
 * Safe to run more than once: each item is only inserted if a "sell" item with the same
 * name doesn't exist yet (see seedItems() in seed_lib.php).
 *
 * itemType decides the page: 'Kitchen' -> kitchen.php, 'Clothes' -> clothes.php,
 * anything else ('Others', like the Add item form uses) -> others.php.
 * Images live in images/sale-items/ (imageURL "sale-items/<file>.svg").
 */

require __DIR__ . '/seed_lib.php';

// [itemName, price RM, category, condition, description, wants (unused for sale), owner, imageURL]
$items = [
    // ---------- Kitchen ----------
    ['Rice Cooker (1.8L)', 45.00, 'Kitchen', 'Good',
        'Family-size 1.8L rice cooker with steamer tray. Great for sharing meals with housemates.',
        null, 'Nabilah Hassan', 'sale-items/rice-cooker-large.svg'],
    ['Air Fryer (3.5L)', 90.00, 'Kitchen', 'Like New',
        'Digital air fryer, used for one semester. Basket is non-stick and still spotless. Box included.',
        null, 'Ong Jia Hui', 'sale-items/air-fryer.svg'],
    ['Electric Kettle (1.5L)', 25.00, 'Kitchen', 'Good',
        'White plastic kettle with auto shut-off and boil-dry protection. Boils water in about 3 minutes.',
        null, 'Haziq Iskandar', 'sale-items/kettle-white.svg'],
    ['Ceramic Plates Set (6 pcs)', 20.00, 'Kitchen', 'Like New',
        'Six matching dinner plates, no chips or cracks. Microwave safe.',
        null, 'Priya Devi', 'sale-items/plates-set.svg'],
    ['Non-stick Frying Pan (26cm)', 18.00, 'Kitchen', 'Good',
        'Works on gas and induction. Coating still smooth, eggs slide right off.',
        null, 'Syafiq Azman', 'sale-items/frying-pan.svg'],
    ['Mini Fridge (46L)', 280.00, 'Kitchen', 'Good',
        'Compact fridge with small freezer compartment. Quiet enough for a hostel room. Self pick-up at Kolej 10.',
        null, 'Wong Zi Xuan', 'sale-items/mini-fridge.svg'],
    ['Blender (1.5L)', 55.00, 'Kitchen', 'Good',
        '2-speed blender with pulse function. Perfect for smoothies and sambal. Blades still sharp.',
        null, 'Aisyah Humaira', 'sale-items/blender.svg'],
    ['Food Containers (set of 10)', 15.00, 'Kitchen', 'Like New',
        'BPA-free containers with lids in 3 sizes. Good for meal prep and storing leftovers.',
        null, 'Daniel Lee', 'sale-items/food-containers.svg'],
    ['Electric Multi Cooker (Hostel Pot)', 35.00, 'Kitchen', 'Fair',
        'Small electric pot for maggi, soup and steamboat. Works fine, some scratches on the outside.',
        null, 'Irfan Hakimi', 'sale-items/multi-cooker.svg'],

    // ---------- Clothes ----------
    ['Oversized Hoodie (L)', 35.00, 'Clothes', 'Good',
        'Soft fleece-lined hoodie in lavender. Perfect for cold lecture halls.',
        null, 'Sarah Lim', 'sale-items/hoodie.svg'],
    ['Baju Kurung (Size M)', 60.00, 'Clothes', 'Like New',
        'Pink floral baju kurung with green kain, worn once for Hari Raya. Dry-cleaned.',
        null, 'Nur Aqilah', 'sale-items/baju-kurung.svg'],
    ['Formal Kemeja (Size M)', 25.00, 'Clothes', 'Good',
        'White long-sleeve formal shirt. Ideal for presentations, interviews and internship.',
        null, 'Adam Firdaus', 'sale-items/kemeja.svg'],
    ['Denim Jeans (W30)', 40.00, 'Clothes', 'Good',
        'Straight-cut dark blue jeans, waist 30, length 32. No tears or fading.',
        null, 'Jason Tan', 'sale-items/jeans.svg'],
    ['Faculty Futsal Jersey (L)', 30.00, 'Clothes', 'Like New',
        'Red futsal jersey from the inter-faculty tournament, number 10. Breathable dri-fit fabric.',
        null, 'Aiman Zulkifli', 'sale-items/jersey.svg'],
    ['White Sneakers (UK 7)', 85.00, 'Clothes', 'Good',
        'Clean white casual sneakers, worn a few times to class. Comes with spare laces.',
        null, 'Chloe Ng', 'sale-items/sneakers.svg'],
    ['Bomber Jacket (M)', 55.00, 'Clothes', 'Good',
        'Olive bomber jacket with orange lining. Light enough for evening rides around campus.',
        null, 'Rizal Ahmad', 'sale-items/bomber-jacket.svg'],
    ['Graduation Robe (Size M)', 80.00, 'Clothes', 'Like New',
        'Bachelor\'s graduation robe with mortarboard, worn once at convocation. Save on rental fees!',
        null, 'Hana Batrisyia', 'sale-items/graduation-robe.svg'],
    ['Lab Coat (Size M)', 20.00, 'Clothes', 'Good',
        'White lab coat for chemistry and biology practicals. Washed and ironed, no stains.',
        null, 'Deepa Krishnan', 'sale-items/lab-coat.svg'],

    // ---------- Others ----------
    ['Programming Textbooks (C++ & Java)', 55.00, 'Others', 'Good',
        'Two textbooks used for first-year programming courses. A few notes in pencil, easy to erase.',
        null, 'Yusuf Rahim', 'sale-items/programming-books.svg'],
    ['Scientific Calculator (Casio fx-991)', 50.00, 'Others', 'Like New',
        'Exam-approved calculator with solar backup. Comes with cover and manual.',
        null, 'Lee Shu Fen', 'sale-items/calculator-fx991.svg'],
    ['Foldable Study Table', 60.00, 'Others', 'Good',
        'Wooden foldable table, 100 x 50 cm. Sturdy enough for a laptop and books, folds flat for moving out.',
        null, 'Hakim Roslan', 'sale-items/study-table.svg'],
    ['Extension Plug (4-way, with USB)', 18.00, 'Others', 'Like New',
        'SIRIM-approved 4-socket extension with 2 USB ports and surge protection. 3m cable.',
        null, 'Anis Sofia', 'sale-items/extension-plug.svg'],
    ['Mountain Bike (27.5")', 350.00, 'Others', 'Good',
        '21-speed mountain bike with front suspension. Serviced last month, new brake pads.',
        null, 'Ravi Shankar', 'sale-items/mountain-bike.svg'],
    ['Wireless Headphones', 120.00, 'Others', 'Good',
        'Over-ear Bluetooth headphones, around 20 hours of battery life. Great for studying in noisy hostels.',
        null, 'Kelvin Chua', 'sale-items/headphones.svg'],
    ['4-Tier Bookshelf', 45.00, 'Others', 'Fair',
        'Wooden bookshelf, 120 cm tall. A few scratches on the side but solid. Self pick-up only.',
        null, 'Zarina Yusof', 'sale-items/bookshelf.svg'],
    ['Hostel Single Mattress', 80.00, 'Others', 'Good',
        'Single-size foam mattress, 4 inches thick. Always used with a protector, no stains.',
        null, 'Faiz Kamarul', 'sale-items/mattress.svg'],
    ['Wireless Keyboard & Mouse Set', 40.00, 'Others', 'Like New',
        'Full-size keyboard and mouse with one USB receiver. Batteries included.',
        null, 'Michelle Goh', 'sale-items/keyboard-mouse.svg'],
];

$conn = seedConnect();
ensureItemColumns($conn);
seedItems($conn, 'sell', $items);
$conn->close();
