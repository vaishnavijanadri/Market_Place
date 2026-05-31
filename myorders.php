<?php

session_start();

if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

$my_id = $_SESSION['user_id'];

// Fetch purchases (items bought by me)
$purchases_query = "SELECT o.order_id, o.amount, o.payment_method, o.order_date, i.title, u.username AS seller_name
                    FROM orders o
                    INNER JOIN items i ON o.item_id = i.item_id
                    INNER JOIN users u ON i.seller_id = u.user_id
                    WHERE o.buyer_id = '$my_id'
                    ORDER BY o.order_date DESC";
$purchases_result = mysqli_query($conn, $purchases_query);

// Fetch sales (items sold by me)
$sales_query = "SELECT o.order_id, o.amount, o.payment_method, o.order_date, i.title, u.username AS buyer_name
                FROM orders o
                INNER JOIN items i ON o.item_id = i.item_id
                INNER JOIN users u ON o.buyer_id = u.user_id
                WHERE i.seller_id = '$my_id'
                ORDER BY o.order_date DESC";
$sales_result = mysqli_query($conn, $sales_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders & Sales</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4b49ac;
            --primary-hover: #3734a9;
            --background: #f4f6f9;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --border: #e2e8f0;
            --success: #10b981;
            --purple-light: #eef2f7;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
        }

        .top-buttons {
            text-align: center;
            margin-bottom: 30px;
        }

        .top-buttons a {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(75, 73, 172, 0.15);
            display: inline-block;
        }

        .top-buttons a:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(75, 73, 172, 0.2);
        }

        .section-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
            border: 1px solid var(--border);
            overflow-x: auto;
        }

        .section-card h3 {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid var(--purple-light);
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: var(--purple-light);
            color: var(--primary);
            padding: 14px 16px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 15px;
            color: var(--text-main);
        }

        tr:hover td {
            background-color: #fafafa;
        }

        .no-records {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
            font-size: 16px;
        }

        .badge-method {
            background: #edf2f7;
            color: var(--text-main);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .price-text {
            font-weight: 600;
            color: var(--success);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📋 Order & Sales History</h2>

    <div class="top-buttons">
        <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>

    <!-- Purchases Section -->
    <div class="section-card">
        <h3>🛍 My Purchases (Bought Items)</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Item Purchased</th>
                    <th>Seller</th>
                    <th>Amount Paid</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($purchases_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($purchases_result)): ?>
                        <tr>
                            <td>#<?php echo $row['order_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                            <td class="price-text">₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td><span class="badge-method"><?php echo htmlspecialchars($row['payment_method']); ?></span></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($row['order_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-records">You haven't bought any items yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Sales Section -->
    <div class="section-card">
        <h3>💰 My Sales (Sold Items)</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Item Sold</th>
                    <th>Buyer</th>
                    <th>Amount Received</th>
                    <th>Payment Method</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($sales_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($sales_result)): ?>
                        <tr>
                            <td>#<?php echo $row['order_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                            <td class="price-text">₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td><span class="badge-method"><?php echo htmlspecialchars($row['payment_method']); ?></span></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($row['order_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-records">You haven't sold any items yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
