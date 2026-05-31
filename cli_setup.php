<?php
$host = "localhost";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS marketplacedb");
mysqli_select_db($conn, "marketplacedb");

mysqli_query($conn, "DROP TABLE IF EXISTS orders");
mysqli_query($conn, "DROP TABLE IF EXISTS notifications");
mysqli_query($conn, "DROP TABLE IF EXISTS items");
mysqli_query($conn, "DROP TABLE IF EXISTS categories");
mysqli_query($conn, "DROP TABLE IF EXISTS users");

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
        echo "Error creating table $table: " . mysqli_error($conn) . "\n";
        $success = false;
    } else {
        echo "Table $table created successfully.\n";
    }
}

if ($success) {
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
    echo "Categories seeded successfully.\n";
    echo "Database setup complete!\n";
}
?>
