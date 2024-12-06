<?php

@include 'config.php';

session_start();

$vendor_id = $_SESSION['vendor_id'];

if(!isset($vendor_id)){
   header('location:vendor_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'vendor_header.php'; ?>

<section class="dashboard">

   <h1 class="title">dashboard</h1>

   <div class="box-container">


 
      <div class="box">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` where vendor_id=?");
         $select_products->execute([$vendor_id]);
         $number_of_products = $select_products->rowCount();
      ?>
      <h3><?= $number_of_products; ?></h3>
      <p>products added</p>
      <a href="vendor_products.php" class="btn">see products</a>
      </div>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>