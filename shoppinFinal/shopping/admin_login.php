<?php
include 'config.php';
session_start();

if(isset($_POST['admin_login'])){
   $username = mysqli_real_escape_string($conn, $_POST['username']);
   $password = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select = mysqli_query($conn, "SELECT * FROM `admin` WHERE username = '$username' AND password = '$password'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $row = mysqli_fetch_assoc($select);
      $_SESSION['admin_id'] = $row['id'];
      header('location:admin_panel.php');
   }else{
      $message[] = 'incorrect username or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <!-- Bootstrap CSS -->
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <!-- Custom CSS -->
   <style>
       body {
           background: linear-gradient(135deg, #d3cce3, #e9e4f0); /* Soft lavender gradient background */
           display: flex;
           justify-content: center;
           align-items: center;
           height: 100vh;
           margin: 0;
           font-family: Arial, sans-serif;
       }

       .form-container {
    background-color: rgba(255, 255, 255, 0.85);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    text-align: center;
    position: relative; /* Ajouté pour que le message d'erreur puisse être positionné relativement */
}


       h3 {
           font-weight: bold;
           margin-bottom: 20px;
           color: #333; /* Darker text color for better contrast */
       }

       .form-control {
           background-color: rgba(255, 255, 255, 0.9);
           border: none;
           padding: 10px 15px;
           margin-bottom: 15px;
           border-radius: 25px;
       }

       .btn {
           background-color: #6a0dad; /* Button color */
           border: none;
           color: #ffffff;
           border-radius: 25px;
           padding: 10px;
           width: 100%;
           font-size: 16px;
           margin-top: 10px;
       }

       .btn:hover {
           background-color: #520b9b; /* Darker purple on hover */
       }

       .form-check-label, .form-text {
           color: #333; /* Darker text color for better contrast */
           font-size: 14px;
       }

       .form-check-label a, .form-text a {
           color: #6a0dad; /* Matching link color */
           text-decoration: underline;
       }

       .form-check-label a:hover, .form-text a:hover {
           color: #520b9b;
       }
       .message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 5px;
    border-radius: 3px;
    margin-bottom: 10px; /* Augmentez légèrement la marge pour qu'elle s'affiche bien entre les éléments */
    font-size: 12px;
    text-align: left;
    width: 100%;
    box-sizing: border-box;
    position: absolute;
    top: -40px; /* Place le message juste au-dessus du champ "username" */
    left: 0;
}


   </style>
</head>
<body>

<div class="form-container">
   <h3>Admin Login</h3>
   <form action="" method="post">
      <?php
      if(isset($message)){
         foreach($message as $message){
            echo '<div class="message">'.$message.'</div>';
         }
      }
      ?>
      <input type="text" name="username" required placeholder="Enter username" class="form-control">
      <input type="password" name="password" required placeholder="Enter password" class="form-control">
      <button type="submit" name="admin_login" class="btn">Login now</button>
   </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
