<?php
$host = "localhost";
$username = "root";
$password = "";

// 1. Establish connection to MySQL server
$conn = mysqli_connect($host, $username, $password);
if (!$conn) {
    die("Database Server Connection Failed: " . mysqli_connect_error());
}

$message = "";
$status = "pending";

if (isset($_POST['install'])) {
    // 2. Create database
    $db_create = "CREATE DATABASE IF NOT EXISTS marketplacedb";
    if (mysqli_query($conn, $db_create)) {
        mysqli_select_db($conn, "marketplacedb");

        // 3. Drop existing tables to avoid conflict and build clean relations
        // Drop in reverse order of foreign key dependencies
        mysqli_query($conn, "DROP TABLE IF EXISTS orders");
        mysqli_query($conn, "DROP TABLE IF EXISTS notifications");
        mysqli_query($conn, "DROP TABLE IF EXISTS items");
        mysqli_query($conn, "DROP TABLE IF EXISTS categories");
        mysqli_query($conn, "DROP TABLE IF EXISTS users");

        // 4. Create Tables
        $queries = [
            "users" => "CREATE TABLE users (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB",

            "categories" => "CREATE TABLE categories (
                category_id INT AUTO_INCREMENT PRIMARY KEY,
                category_name VARCHAR(50) NOT NULL UNIQUE,
                description VARCHAR(255) NULL
            ) ENGINE=InnoDB",

            "items" => "CREATE TABLE items (
                item_id INT AUTO_INCREMENT PRIMARY KEY,
                seller_id INT NOT NULL,
                title VARCHAR(100) NOT NULL,
                description TEXT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                category_id INT NOT NULL,
                item_condition VARCHAR(50) NOT NULL,
                status ENUM('Available', 'Sold') DEFAULT 'Available',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT
            ) ENGINE=InnoDB",

            "orders" => "CREATE TABLE orders (
                order_id INT AUTO_INCREMENT PRIMARY KEY,
                buyer_id INT NOT NULL,
                item_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                payment_method VARCHAR(50) NOT NULL,
                order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (buyer_id) REFERENCES users(user_id) ON DELETE CASCADE,
                FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
            ) ENGINE=InnoDB",

            "notifications" => "CREATE TABLE notifications (
                notif_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                message TEXT NOT NULL,
                is_read ENUM('Unread', 'Read') DEFAULT 'Unread',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            ) ENGINE=InnoDB"
        ];

        $success = true;
        foreach ($queries as $table => $sql) {
            if (!mysqli_query($conn, $sql)) {
                $message .= "❌ Error creating table `$table`: " . mysqli_error($conn) . "<br>";
                $success = false;
            }
        }

        if ($success) {
            // 5. Seed categories
            $categories = [
                ['Electronics', 'Gadgets, phones, laptops, and accessories'],
                ['Books', 'Textbooks, novels, and educational materials'],
                ['Fashion', 'Clothing, shoes, bags, and accessories'],
                ['Home & Furniture', 'Household items, decor, and furniture'],
                ['Vehicles', 'Bicycles, motorbikes, and car accessories'],
                ['Sports', 'Fitness gear and outdoor equipment'],
                ['Other', 'Miscellaneous items']
            ];

            foreach ($categories as $cat) {
                $name = mysqli_real_escape_string($conn, $cat[0]);
                $desc = mysqli_real_escape_string($conn, $cat[1]);
                mysqli_query($conn, "INSERT INTO categories (category_name, description) VALUES ('$name', '$desc')");
            }

            $message = "🎉 Database and relational schema initialized successfully! <br> Relational constraints and categories seeded.";
            $status = "success";
        } else {
            $status = "error";
        }
    } else {
        $message = "❌ Error creating database: " . mysqli_error($conn);
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup Wizard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4b49ac;
            --primary-hover: #3734a9;
            --background: #f4f6f9;
            --card-bg: #ffffff;
            --text-main: #2d3748;
            --text-muted: #718096;
            --success: #10b981;
            --error: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-main);
        }

        .container {
            width: 100%;
            max-width: 550px;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            transition: all 0.3s ease;
        }

        h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        p.subtitle {
            font-size: 15px;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .status-box {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 16px;
            line-height: 1.6;
            text-align: left;
        }

        .status-box.success {
            background-color: #ecfdf5;
            border-left: 5px solid var(--success);
            color: #065f46;
        }

        .status-box.error {
            background-color: #fef2f2;
            border-left: 5px solid var(--error);
            color: #991b1b;
        }

        .db-info {
            background: #f7fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }

        .db-info h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .db-info ul {
            list-style: none;
        }

        .db-info li {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .db-info li span.badge {
            background: #e2e8f0;
            color: var(--text-main);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(75, 73, 172, 0.2);
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(75, 73, 172, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: #edf2f7;
            color: var(--text-main);
            box-shadow: none;
            margin-top: 12px;
            text-decoration: none;
            display: block;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            box-shadow: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>DBMS Database Setup Wizard</h2>
    <p class="subtitle">Initialize the normalized database schema for the marketplace</p>

    <?php if ($status === "success"): ?>
        <div class="status-box success">
            <?php echo $message; ?>
        </div>
        <a href="login.php" class="btn">Go to Login</a>
    <?php elseif ($status === "error"): ?>
        <div class="status-box error">
            <?php echo $message; ?>
        </div>
        <form method="POST">
            <button type="submit" name="install" class="btn">Retry Installation</button>
        </form>
    <?php else: ?>
        <div class="db-info">
            <h3>Schema details to create:</h3>
            <ul>
                <li><span>Database Name:</span> <strong>marketplacedb</strong></li>
                <li><span>Users Table</span> <span class="badge">1:N with Items</span></li>
                <li><span>Categories Table</span> <span class="badge">1:N with Items</span></li>
                <li><span>Items Table</span> <span class="badge">Relational Link</span></li>
                <li><span>Orders Table</span> <span class="badge">N:M via users & items</span></li>
                <li><span>Notifications Table</span> <span class="badge">Log center</span></li>
            </ul>
        </div>
        
        <form method="POST">
            <button type="submit" name="install" class="btn">Initialize Relational Database</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
