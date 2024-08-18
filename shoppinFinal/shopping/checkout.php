<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_POST['place_order'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $payment_method = 'paypal';  // Méthode de paiement définie à PayPal

    if (empty($name) || empty($phone) || empty($address) || empty($city) || empty($country) || empty($zip)) {
        $message[] = 'Veuillez remplir tous les champs';
    } else {
        // Calculer le total de la commande
        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die(mysqli_error($conn));
        $total_price = 0;
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $total_price += $cart_item['price'] * $cart_item['quantity'];
        }

        // Insérer les détails de la commande avec le total de la commande
        $insert_order = mysqli_query($conn, "INSERT INTO `orders` (user_id, name, phone, address, city, country, zip, total_price) VALUES ('$user_id', '$name', '$phone', '$address', '$city', '$country', '$zip', '$total_price')") or die('query failed');

        if ($insert_order) {
            $order_id = mysqli_insert_id($conn);

            // Insérer chaque article du panier dans order_items
            $cart_items = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'") or die('query failed');
            while ($item = mysqli_fetch_assoc($cart_items)) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '$product_id', '$quantity', '$price')") or die('query failed');
            }

            // Supprimer les articles du panier après la commande
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'") or die('query failed');

            // Afficher un message de succès et rediriger vers la page de paiement
            $_SESSION['order_id'] = $order_id;
            header('location:payment.php?method=' . $payment_method);
        } else {
            $message[] = 'Order could not be placed';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1.heading {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 25px;
            padding: 10px;
        }

        .btn-primary {
            background-color: #6a0dad;
            border: none;
            color: #ffffff;
            border-radius: 25px;
            padding: 10px;
            font-size: 16px;
            margin-top: 20px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #520b9b;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            color: #ffffff;
            border-radius: 25px;
            padding: 10px;
            font-size: 16px;
            margin-top: 10px;
            width: 100%;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="heading">Commande</h1>
    <form action="" method="post">
        <div class="form-group">
            <label for="name">Nom Complet</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">Adresse</label>
            <input type="text" name="address" id="address" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="city">Ville</label>
            <input type="text" name="city" id="city" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="country">Pays</label>
            <input type="text" name="country" id="country" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="zip">Code Postal</label>
            <input type="text" name="zip" id="zip" class="form-control" required>
        </div>
        <button type="submit" name="place_order" class="btn btn-primary">Proceed to Checkout</button>
        <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
    </form>
    <?php
    if (isset($message)) {
        foreach ($message as $msg) {
            echo '<div class="alert alert-danger mt-3">' . $msg . '</div>';
        }
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
