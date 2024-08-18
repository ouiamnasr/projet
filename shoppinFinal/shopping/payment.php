<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

$payment_method = isset($_GET['method']) ? $_GET['method'] : '';

$order_id = $_SESSION['order_id'];
$order_query = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE id = '$order_id'") or die(mysqli_error($conn));
$order = mysqli_fetch_assoc($order_query);
$total_price = $order['total_price'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .payment-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .heading {
            font-weight: bold;
            margin-bottom: 20px;
            color: #333; /* Darker text color for better contrast */
        }

        .btn-secondary {
            background-color: #6a0dad;
            border: none;
            color: #ffffff;
            border-radius: 25px;
            padding: 10px;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn-secondary:hover {
            background-color: #520b9b; /* Darker purple on hover */
        }

        #paypal-button-container {
            margin-top: 30px;
        }
    </style>
    <!-- PayPal Checkout Script -->
    <script src="https://www.paypal.com/sdk/js?client-id=AURoClKTofgpvyisGertGKUDBCL8EeB_3z1ytZ3X6A9pndUHk9dKjIncdTTmL8-wn4XxW6hGxH3FRakG&currency=CAD"></script>
</head>

<body>

<div class="payment-container">
    <h1 class="heading">Paiement Avec Paypal</h1>

    <?php 
    if ($payment_method == 'paypal') { ?>
        <div id="paypal-button-container"></div>
        <script>
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?php echo $total_price; ?>' // Le montant total de la transaction
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert('Transaction completed by ' + details.payer.name.given_name);
                        window.location.href = "success.php?orderID=" + data.orderID;
                    });
                }
            }).render('#paypal-button-container');
        </script>
    <?php }  
    else { ?>
        <p>Méthode de paiement sélectionnée invalide.</p>
    <?php } ?>

    <a href="checkout.php" class="btn btn-secondary">Retour à la Commande</a>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcs.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
