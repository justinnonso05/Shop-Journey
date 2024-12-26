<?php
$host = 'localhost';
$dbname = 'shop_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

$imageDir = 'uploads/';

if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $image = $_FILES['image'] ?? null;

    if ($name && $description && $price && $image) {
        $imagePath = '';
        if ($image['error'] === UPLOAD_ERR_OK) {
            $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('product_', true) . '.' . $imageExtension;
            $imagePath = $imageDir . $imageName;

            if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $imagePath]);
                header("Location: products.php");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./css/products.css">
    <title>Add Product</title>
</head>
<body>
    <nav>
        <div class="nav-content">
            <div class="logo">MY JOURNEY</div>
            <div class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#journey">Journey</a></li>
                <li><a href="products.php">Shop</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="form-container">
            <h2>Add a Product</h2>
            <form method="POST" action="" enctype="multipart/form-data" class="product-form">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Product Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (USD)</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                
                <button type="submit" class="submit-btn">Add Product</button>
            </form>
            <a href="products.php" class="view-products-btn">View All Products</a>
        </div>
    </main>

    <script>
        document.getElementById('navToggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>