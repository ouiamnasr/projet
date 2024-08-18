<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location:login.php');
    exit;
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE product_id = '$product_id' AND user_id = '$user_id'") or die(mysqli_error($conn));

    if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'Product already added to cart!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, product_id, name, price, description, image, quantity) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_description', '$product_image', '$product_quantity')") or die(mysqli_error($conn));
        $message[] = 'Product added to cart!';
    }
}

if (isset($_POST['update_cart'])) {
    $update_quantity = $_POST['cart_quantity'];
    $update_id = $_POST['cart_id'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die(mysqli_error($conn));
    $message[] = 'Cart quantity updated successfully!';
}

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die(mysqli_error($conn));
    header('location:index.php');
}

if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die(mysqli_error($conn));
    header('location:index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Shopping Cart</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #d3cce3, #e9e4f0); /* Matching the background gradient */
            margin: 0;
            padding: 0;
        }

        .navbar-brand {
            font-weight: bold;
            letter-spacing: 1px;
            color: #6a0dad; /* Matching violet color */
        }

        .product-card {
            transition: transform 0.2s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }

        .product-card:hover {
            transform: scale(1.02);
        }

        .product-img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .footer {
            background-color: #343a40;
            color: #ffffff;
            padding: 20px 0;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
        }

        .table thead {
            background-color: #6a0dad; /* Matching violet color */
            color: #ffffff;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .quantity-input {
            width: 70px;
            display: inline-block;
        }

        .alert-position {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 1000;
        }

        .btn-primary {
            background-color: #6a0dad;
            border: none;
            color: #ffffff;
            border-radius: 25px;
            padding: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #520b9b;
        }

        .btn-info {
            background-color: #5a5a5a;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        @media (max-width: 767.98px) {
            .product-img {
                height: 150px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">MyShop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <?php
            $select_user = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die(mysqli_error($conn));
            if (mysqli_num_rows($select_user) > 0) {
                $fetch_user = mysqli_fetch_assoc($select_user);
            }
            ?>
            <li class="nav-item">
                <a class="nav-link" href="#">Hello, <?php echo $fetch_user['name']; ?>!</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Alert Messages -->
<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<div class="alert alert-info alert-dismissible fade show alert-position" role="alert">
                ' . $msg . '
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }
}
?>

<!-- Main Content -->
<div class="container my-5">
    <!-- Products Section -->
    <h2 class="mb-4">Latest Products</h2>

    <!-- Filter Form -->
    <form action="" method="get" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="category_filter">Category</label>
                <select name="category_filter" id="category_filter" class="form-control">
                    <option value="">All Categories</option>
                    <?php
                    $category_query = mysqli_query($conn, "SELECT * FROM `categories`") or die(mysqli_error($conn));
                    if (mysqli_num_rows($category_query) > 0) {
                        while ($category = mysqli_fetch_assoc($category_query)) {
                            $selected = (isset($_GET['category_filter']) && $_GET['category_filter'] == $category['id']) ? 'selected' : '';
                            echo '<option value="' . $category['id'] . '" ' . $selected . '>' . $category['name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="price_min_filter">Min Price</label>
                <input type="number" name="price_min_filter" id="price_min_filter" class="form-control" placeholder="Enter min price" value="<?php echo isset($_GET['price_min_filter']) ? $_GET['price_min_filter'] : ''; ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="price_max_filter">Max Price</label>
                <input type="number" name="price_max_filter" id="price_max_filter" class="form-control" placeholder="Enter max price" value="<?php echo isset($_GET['price_max_filter']) ? $_GET['price_max_filter'] : ''; ?>">
            </div>
            <div class="form-group col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php
        // Filtrage des produits
        $category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : '';
        $price_min_filter = isset($_GET['price_min_filter']) ? $_GET['price_min_filter'] : '';
        $price_max_filter = isset($_GET['price_max_filter']) ? $_GET['price_max_filter'] : '';

        $query = "SELECT * FROM `products` WHERE 1";

        if (!empty($category_filter)) {
            $query .= " AND category_id = '$category_filter'";
        }

        if (!empty($price_min_filter)) {
            $query .= " AND price >= '$price_min_filter'";
        }

        if (!empty($price_max_filter)) {
            $query .= " AND price <= '$price_max_filter'";
        }

        $select_product = mysqli_query($conn, $query) or die(mysqli_error($conn));

        if (mysqli_num_rows($select_product) > 0) {
            while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <img src="images/<?php echo $fetch_product['image']; ?>" class="card-img-top product-img" alt="<?php echo $fetch_product['name']; ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $fetch_product['name']; ?></h5>
                            <p class="card-text text-truncate"><?php echo $fetch_product['description']; ?></p>
                            <h5 class="mt-auto">$<?php echo number_format($fetch_product['price'], 2); ?></h5>
                            <form method="post" class="mt-3">
                                <input type="hidden" name="product_id" value="<?php echo $fetch_product['id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                                <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                                <input type="hidden" name="product_description" value="<?php echo $fetch_product['description']; ?>">
                                <input type="hidden" name="product_image" value="images/<?php echo $fetch_product['image']; ?>">
                                <div class="input-group mb-3">
                                    <input type="number" name="product_quantity" min="1" value="1" class="form-control" placeholder="Quantity">
                                    <div class="input-group-append">
                                        <button type="submit" name="add_to_cart" class="btn btn-outline-secondary">Add to Cart</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12"><p class="text-center">No products found.</p></div>';
        }
        ?>
    </div>

    <!-- Shopping Cart Section -->
    <h2 class="mt-5 mb-4">Your Shopping Cart</h2>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th width="120px">Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die(mysqli_error($conn));

                if (mysqli_num_rows($select_cart) > 0) {
                    $grand_total = 0;
                    while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                        $cart_id = $fetch_cart['id'];
                        $product_name = $fetch_cart['name'];
                        $product_price = $fetch_cart['price'];
                        $product_quantity = $fetch_cart['quantity'];
                        $total_price = $product_price * $product_quantity;
                        $grand_total += $total_price;
                        ?>
                        <tr>
                            <td>
                            <?php echo $product_name; ?>
                                <br>
                                
                            </td>
                            <td><?php echo $fetch_cart['description']; ?></td>
                            <td>$<?php echo number_format($product_price, 2); ?></td>
                            <td>
                                <form action="" method="post" class="d-flex">
                                    <input type="number" name="cart_quantity" min="1" value="<?php echo $product_quantity; ?>" class="form-control quantity-input">
                                    <input type="hidden" name="cart_id" value="<?php echo $cart_id; ?>">
                                    <button type="submit" name="update_cart" class="btn btn-sm btn-info ml-2">Update</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($total_price, 2); ?></td>
                            <td>
                                <a href="index.php?remove=<?php echo $cart_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this item?');">Remove</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="4" class="text-right font-weight-bold">Grand Total</td>
                        <td colspan="2" class="font-weight-bold">$<?php echo number_format($grand_total, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-right">
                            <a href="index.php?delete_all" class="btn btn-danger mr-2" onclick="return confirm('Are you sure you want to clear the cart?');">Clear Cart</a>
                            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                        </td>
                    </tr>
                    <?php
                } else {
                    echo '<tr><td colspan="6" class="text-center">Your cart is empty.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer class="footer text-center">
    <div class="container">
        <p class="mb-0">© <?php echo date('Y'); ?> MyShop. All rights reserved.</p>
    </div>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
