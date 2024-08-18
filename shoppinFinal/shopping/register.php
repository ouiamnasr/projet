<?php

include 'config.php';

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $message[] = 'User already exists!';
   } else {
      if($pass != $cpass){
         $message[] = 'Passwords do not match!';
      } else {
         mysqli_query($conn, "INSERT INTO `user_form`(name, email, password) VALUES('$name', '$email', '$pass')") or die('query failed');
         $message[] = 'Registered successfully!';
         header('location:login.php');
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <!-- Bootstrap CSS -->
   <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <!-- Custom CSS -->
   <style>
       body {
           background: linear-gradient(135deg, #d3cce3, #e9e4f0);
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
       }

       h3 {
           font-weight: bold;
           margin-bottom: 20px;
           color: #333;
       }

       .form-control {
           background-color: rgba(255, 255, 255, 0.9);
           border: none;
           padding: 10px 15px;
           margin-bottom: 15px;
           border-radius: 25px;
       }

       .btn {
           background-color: #6a0dad;
           border: none;
           color: #ffffff;
           border-radius: 25px;
           padding: 10px;
           width: 100%;
           font-size: 16px;
           margin-top: 10px;
       }

       .btn:hover {
           background-color: #520b9b;
       }

       .form-text {
           color: #333;
           font-size: 14px;
       }

       .form-text a {
           color: #6a0dad;
           text-decoration: underline;
       }

       .form-text a:hover {
           color: #520b9b;
       }

       .message {
           background-color: #f8d7da;
           color: #721c24;
           padding: 5px;
           border-radius: 3px;
           margin-bottom: 10px;
           font-size: 12px;
           text-align: left;
           width: 100%;
           box-sizing: border-box;
           position: relative;
           top: -10px;
       }
   </style>
</head>
<body>

<div class="form-container">
   <h3>Register Now</h3>
   <form action="" method="post">
      <?php
      if(isset($message)){
         foreach($message as $message){
            echo '<div class="message">'.$message.'</div>';
         }
      }
      ?>
      <input type="text" name="name" required placeholder="Enter username" class="form-control">
      <input type="email" name="email" required placeholder="Enter email" class="form-control">
      <input type="password" name="password" required placeholder="Enter password" class="form-control">
      <input type="password" name="cpassword" required placeholder="Confirm password" class="form-control">
      <button type="submit" name="submit" class="btn">Register Now</button>
      <p class="form-text">Already have an account? <a href="login.php">Login now</a></p>
   </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
