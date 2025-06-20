<?php
session_start();
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    die("Access denied. Only admins can view this page.");
}

$conn = new mysqli("localhost", "root", "", "eco_marketplace");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $conn->query("DELETE FROM items WHERE itemID = $deleteId");
}

$itemsResult = $conn->query("SELECT * FROM items");
$usersResult = $conn->query("SELECT * FROM users WHERE role != 'admin'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - EcoMarketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9fdfb;
            padding: 30px;
        }
        .container {
            max-width: 95%;
            margin: auto;
        }
        h2 {
            margin-top: 40px;
            margin-bottom: 20px;
            color: #206444;
            font-weight: 600;
        }
        table {
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        th {
            background-color: #eaf6ec;
            color: #206444;
        }
        th, td {
            padding: 12px 15px;
        }
        tr:hover {
            background-color: #f2fdf4;
        }
        .delete-link {
            color: red;
            font-weight: bold;
        }
        .delete-link:hover {
            text-decoration: underline;
        }
        .dashboard-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #2d6a4f;
            margin-bottom: 40px;
        }
        img {
            border-radius: 6px;
            max-width: 70px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="dashboard-title">🌿 Admin Dashboard | EcoMarketplace</div>

    <h2>📦 All Listed Items</h2>
    <table class="table table-bordered table-hover">
        <thead>
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
        </thead>
        <tbody>
            <?php while ($item = $itemsResult->fetch_assoc()): ?>
            <tr>
                <td><?= $item['itemID'] ?></td>
                <td><?= htmlspecialchars($item['itemName']) ?></td>
                <td><?= $item['purpose'] ?></td>
                <td><?= $item['itemType'] ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= $item['phoneNumber'] ?></td>
                <td><img src="../project-folder/<?= $item['imageURL'] ?>" alt="item"></td>
                <td>
                    <a class="delete-link" href="?delete_id=<?= $item['itemID'] ?>" onclick="return confirm('Are you sure you want to delete this item?')">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>👥 Registered Users</h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $usersResult->fetch_assoc()): ?>
            <tr>
                <td><?= $user['userID'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= $user['role'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
