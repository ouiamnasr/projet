<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_GET['logout'])) {
    unset($admin_id);
    session_destroy();
    header('location:admin_login.php');
}

if (isset($_POST['add_product'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'images/' . $product_image;

    if (empty($product_name) || empty($product_price) || empty($product_description) || empty($product_image) || empty($category_id)) {
        $message[] = 'Please fill out all fields';
    } else {
        $insert_query = "INSERT INTO `products` (name, price, description, category_id, image) VALUES ('$product_name', '$product_price', '$product_description', '$category_id', '$product_image')";
        $insert = mysqli_query($conn, $insert_query) or die(mysqli_error($conn));

        if ($insert) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            $message[] = 'Product added successfully';
        } else {
            $message[] = 'Product could not be added';
        }
    }
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_p_name = mysqli_real_escape_string($conn, $_POST['update_p_name']);
    $update_p_price = mysqli_real_escape_string($conn, $_POST['update_p_price']);
    $update_p_description = mysqli_real_escape_string($conn, $_POST['update_p_description']);
    $update_category_id = mysqli_real_escape_string($conn, $_POST['update_category_id']);
    $update_p_image = $_FILES['update_p_image']['name'];
    $update_p_image_tmp_name = $_FILES['update_p_image']['tmp_name'];
    $update_p_image_folder = 'images/' . $update_p_image;

    if (empty($update_p_image)) {
        $update_query = "UPDATE `products` SET name = '$update_p_name', price = '$update_p_price', description = '$update_p_description', category_id = '$update_category_id' WHERE id = '$update_p_id'";
    } else {
        $update_query = "UPDATE `products` SET name = '$update_p_name', price = '$update_p_price', description = '$update_p_description', category_id = '$update_category_id', image = '$update_p_image' WHERE id = '$update_p_id'";
        move_uploaded_file($update_p_image_tmp_name, $update_p_image_folder);
    }

    $update = mysqli_query($conn, $update_query) or die(mysqli_error($conn));

    if ($update) {
        $message[] = 'Product updated successfully';
    } else {
        $message[] = 'Product could not be updated';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_query = "DELETE FROM `products` WHERE id = '$delete_id'";
    $delete = mysqli_query($conn, $delete_query) or die(mysqli_error($conn));
    if ($delete) {
        $message[] = 'Product deleted successfully';
    } else {
        $message[] = 'Product could not be deleted';
    }
}

if (isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    if (empty($category_name)) {
        $message[] = 'Please enter a category name';
    } else {
        $insert_query = "INSERT INTO `categories` (name) VALUES ('$category_name')";
        $insert = mysqli_query($conn, $insert_query) or die(mysqli_error($conn));
        if ($insert) {
            $message[] = 'Category added successfully';
        } else {
            $message[] = 'Category could not be added';
        }
    }
}

if (isset($_GET['delete_category'])) {
    $delete_id = $_GET['delete_category'];
    $delete_query = "DELETE FROM `categories` WHERE id = '$delete_id'";
    $delete = mysqli_query($conn, $delete_query) or die(mysqli_error($conn));
    if ($delete) {
        $message[] = 'Category deleted successfully';
    } else {
        $message[] = 'Category could not be deleted';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Couleur d'arrière-plan */
        body {
            background-color: #E1DBF3;
        }

        /* Ajustement des boutons */
        .btn {
            margin-right: 5px;
            padding: 5px 15px;
            width: 170px;
            background-color: #E1DBF3;
            color: black;
            font-weight: bold;
            border: 2px solid #7b1fa2;
            white-space: nowrap;
            text-align: center;
        }

        .btn:hover {
            background-color: #d3cce3;
        }

        /* Agrandir la section */
        .manage-section {
            margin: 20px;
            width: 96%;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .manage-orders {
    margin: 20px auto; /* Centrer horizontalement en ajoutant 'auto' pour les marges latérales */
    width: 96%; /* Ajuster la largeur pour qu'elle soit similaire à la section suivante */
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

        /* Ajustement spécifique pour les cartes */
        .card {
            border-radius: 50px;
        }

        /* Centrer le texte dans le tableau */
        .table th, .table td {
            text-align: center;
        }

        /* Titre des colonnes en italique */
        .table th {
            font-style: italic;
        }

        /* Alignement des labels à gauche */
        .form-group label {
            text-align: left;
            display: block;
            font-style: italic;
            font-weight: bold;
        }

        /* Assurer la même largeur pour les colonnes Name et Actions */
        .table th:first-child, .table td:first-child,
        .table th:last-child, .table td:last-child {
            width: 10%;
        }

        /* Marges entre les boutons et les labels */
        .form-group {
            margin-bottom: 20px;
        }

        /* Marges pour le bouton Go to Login */
        .go-to-login {
            margin-top: 20px;
            background-color: #7b1fa2;
            color: white;
            font-weight: bold;
            margin-left: auto;
            margin-right: auto;
            display: block;
            text-align: center;
            
        }

        /* Largeur maximale pour éviter les débordements */
        .container-fluid {
            max-width: 1500px;
        }

  

    </style>
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '<div class="alert alert-warning" role="alert">' . $message . '</div>';
    }
}
?>

<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4 manage-orders">
                <div class="card-header text-center">
                    <h3>Manage Orders</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User ID</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die(mysqli_error($conn));
                            if (mysqli_num_rows($select_orders) > 0) {
                                while ($order = mysqli_fetch_assoc($select_orders)) {
                            ?>
                                    <tr>
                                        <td><?php echo $order['id']; ?></td>
                                        <td><?php echo $order['user_id']; ?></td>
                                        <td><?php echo $order['name']; ?></td>
                                        <td><?php echo $order['phone']; ?></td>
                                        <td><?php echo $order['address']; ?></td>
                                        <td>
                                            <a href="admin_order_details.php?order_id=<?php echo $order['id']; ?>" class="btn">View Details</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">No orders available</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mb-4 manage-section">
                <div class="card-header text-center">
                    <h3>Add a New Category</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" name="category_name" class="form-control" id="category_name" placeholder="Enter category name" required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-block">Add Category</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mb-4 manage-section">
                <div class="card-header text-center">
                    <h3>Manage Categories</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_categories = mysqli_query($conn, "SELECT * FROM `categories`") or die(mysqli_error($conn));
                            if (mysqli_num_rows($select_categories) > 0) {
                                while ($row = mysqli_fetch_assoc($select_categories)) {
                            ?>
                                    <tr>
                                        <td><?php echo $row['name']; ?></td>
                                        <td>
                                            <a href="admin_panel.php?delete_category=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="2" class="text-center">No categories available</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mb-4 manage-section">
                <div class="card-header text-center">
                    <h3>Add a New Product</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Enter product name" required>
                        </div>
                        <div class="form-group">
                            <label for="product_price">Product Price</label>
                            <input type="number" name="product_price" class="form-control" id="product_price" placeholder="Enter product price" required>
                        </div>
                        <div class="form-group">
                            <label for="product_description">Product Description</label>
                            <textarea name="product_description" class="form-control" id="product_description" placeholder="Enter product description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" class="form-control" id="category_id" required>
                                <option value="">Select Category</option>
                                <?php
                                $category_query = mysqli_query($conn, "SELECT * FROM `categories`") or die(mysqli_error($conn));
                                if (mysqli_num_rows($category_query) > 0) {
                                    while ($category = mysqli_fetch_assoc($category_query)) {
                                        echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No categories available</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product_image">Product Image</label>
                            <input type="file" name="product_image" class="form-control-file" id="product_image" accept="image/png, image/jpeg, image/jpg" required>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-block">Add Product</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mb-4 manage-section">
                <div class="card-header text-center">
                    <h3>Manage Products</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_products = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM `products` p LEFT JOIN `categories` c ON p.category_id = c.id") or die(mysqli_error($conn));
                            if (mysqli_num_rows($select_products) > 0) {
                                while ($row = mysqli_fetch_assoc($select_products)) {
                            ?>
                                    <tr>
                                        <td><img src="images/<?php echo $row['image']; ?>" height="100" alt="<?php echo $row['name']; ?>"></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td>$<?php echo $row['price']; ?></td>
                                        <td><?php echo $row['description']; ?></td>
                                        <td><?php echo $row['category_name']; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="admin_panel.php?edit=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                                <a href="admin_panel.php?delete=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">No products available</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php
        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $edit_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$edit_id'") or die(mysqli_error($conn));
            if (mysqli_num_rows($edit_query) > 0) {
                $fetch_edit = mysqli_fetch_assoc($edit_query);
        ?>
            <div class="col-md-12">
                <div class="card mb-4 manage-section">
                    <div class="card-header text-center">
                        <h3>Edit Product</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
                            <div class="form-group">
                                <label for="update_p_name">Product Name</label>
                                <input type="text" name="update_p_name" class="form-control" id="update_p_name" value="<?php echo $fetch_edit['name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="update_p_price">Product Price</label>
                                <input type="number" name="update_p_price" class="form-control" id="update_p_price" value="<?php echo $fetch_edit['price']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="update_p_description">Product Description</label>
                                <textarea name="update_p_description" class="form-control" id="update_p_description" required><?php echo $fetch_edit['description']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="update_category_id">Category</label>
                                <select name="update_category_id" class="form-control" id="update_category_id" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $category_query = mysqli_query($conn, "SELECT * FROM `categories`") or die(mysqli_error($conn));
                                    if (mysqli_num_rows($category_query) > 0) {
                                        while ($category = mysqli_fetch_assoc($category_query)) {
                                            echo '<option value="' . $category['id'] . '"' . ($fetch_edit['category_id'] == $category['id'] ? ' selected' : '') . '>' . $category['name'] . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No categories available</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="update_p_image">Product Image</label>
                                <input type="file" name="update_p_image" class="form-control-file" id="update_p_image" accept="image/png, image/jpeg, image/jpg">
                                <img src="images/<?php echo $fetch_edit['image']; ?>" height="100" alt="<?php echo $fetch_edit['name']; ?>">
                            </div>
                            <button type="submit" name="update_product" class="btn btn-block">Update Product</button>
                            <a href="admin_panel.php" class="btn btn-secondary btn-block go-to-login">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        <?php
            }
        }
        ?>

        <div class="col-md-12">
            <a href="login.php" class="btn btn-secondary btn-block go-to-login">Go to Login</a>
        </div>

    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
