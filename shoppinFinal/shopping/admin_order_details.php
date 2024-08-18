<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (!isset($_GET['order_id'])) {
    header('location:admin_panel.php');
    exit;
}

$order_id = $_GET['order_id'];
$order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE id = '$order_id'") or die(mysqli_error($conn));
$order_details_query = mysqli_query($conn, "SELECT oi.*, p.name, p.image FROM `order_items` oi JOIN `products` p ON oi.product_id = p.id WHERE oi.order_id = '$order_id'") or die(mysqli_error($conn));

if (mysqli_num_rows($order_query) > 0) {
    $order = mysqli_fetch_assoc($order_query);
} else {
    header('location:admin_panel.php');
    exit;
}

if (isset($_POST['delete_order'])) {
    $delete_order_items = mysqli_query($conn, "DELETE FROM `order_items` WHERE order_id = '$order_id'") or die(mysqli_error($conn));
    $delete_order = mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$order_id'") or die(mysqli_error($conn));
    header('location:admin_panel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #E1DBF3;
        }

        .container {
            margin: 40px auto;
            width: 95%;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 10px;
        }

        h1.heading, h3 {
            text-align: center;
            font-style: italic;
            color: #333;
        }

        .order-info p {
            font-weight: bold;
            color: #555;
        }

        .table th, .table td {
            text-align: center;
        }

        .btn-secondary {
            background-color: #6f42c1;
            color: white;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #563d7c;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn {
            margin: 10px 5px;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="heading">Order Details</h1>
    <div class="order-info">
        <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
        <p><strong>User ID:</strong> <?php echo $order['user_id']; ?></p>
        <p><strong>Name:</strong> <?php echo $order['name']; ?></p>
        <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
        <p><strong>Address:</strong> <?php echo $order['address']; ?>, <?php echo $order['city']; ?>, <?php echo $order['country']; ?>, <?php echo $order['zip']; ?></p>
    </div>
    <h3>Order Items</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Image</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grand_total = 0;
            if (mysqli_num_rows($order_details_query) > 0) {
                while ($item = mysqli_fetch_assoc($order_details_query)) {
                    $total_price = $item['quantity'] * $item['price'];
                    $grand_total += $total_price;
            ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td><img src="images/<?php echo $item['image']; ?>" height="100" alt="<?php echo $item['name']; ?>"></td>
                        <td>$<?php echo $item['price']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo $total_price; ?></td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">No items in this order</td></tr>';
            }
            ?>
        </tbody>
    </table>
    <form action="" method="post">
        <a href="admin_panel.php" class="btn btn-secondary">Back to Orders</a>
        <button type="submit" name="delete_order" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?');">Delete Order</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
