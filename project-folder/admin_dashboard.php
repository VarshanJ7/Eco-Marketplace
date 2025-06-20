<?php
// ✅ Start session to track logged-in user's data
session_start();

// ✅ Check if the user is an admin (case-insensitive)
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    // 🔒 If not admin, block access
    die("Access denied. Only admins can view this page.");
}

// ✅ Connect to the database
$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ If admin clicked a "Delete" button (with ?delete_id in URL), delete that item
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']); // sanitize input
    $conn->query("DELETE FROM items WHERE itemID = $deleteId");
}

// ✅ Fetch all items for admin to view
$itemsResult = $conn->query("SELECT * FROM items");

// ✅ Fetch all users except Admins (so admin doesn’t delete other admins)
$usersResult = $conn->query("SELECT * FROM users WHERE role != 'admin'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        /* ✅ Dashboard styling */
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #f4f4f4;
        }
        h2 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        table, th, td {
            border: 1px solid #bbb;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        a.delete-link {
            color: red;
            text-decoration: none;
        }
        a.delete-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- ✅ Section: Items Table -->
<h2>📦 All Listed Items</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Purpose</th>
        <th>Type</th>
        <th>Price (RM)</th>
        <th>Phone</th>
        <th>Image</th>
        <th>Action</th>
    </tr>
    <?php while ($item = $itemsResult->fetch_assoc()): ?>
    <tr>
        <td><?= $item['itemID'] ?></td>
        <td><?= htmlspecialchars($item['itemName']) ?></td>
        <td><?= $item['purpose'] ?></td>
        <td><?= $item['itemType'] ?></td>
        <td><?= number_format($item['price'], 2) ?></td>
        <td><?= $item['phoneNumber'] ?></td>
        <td><img src="../project-folder/<?= $item['imageURL'] ?>" width="80"></td>
        <td>
            <a class="delete-link" href="?delete_id=<?= $item['itemID'] ?>" onclick="return confirm('Are you sure?')">
                Delete
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- ✅ Section: Registered Users -->
<h2>👥 Registered Users</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
    </tr>
    <?php while ($user = $usersResult->fetch_assoc()): ?>
    <tr>
        <td><?= $user['userID'] ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= $user['role'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
