<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="products">

   <h1 class="title">shops</h1>

   <div class="box-container">
	  
  <?php
  $select_vendor =  $conn->prepare("SELECT * FROM `vendor` " );
  $select_vendor->execute();
   if($select_vendor->rowCount() > 0){
	 while($fetch_vendor = $select_vendor->fetch(PDO::FETCH_ASSOC)){
  ?>
	  
   <form action="" class="box" method="POST">

     <a href="shop.php?vid=<?= $fetch_vendor['id']; ?>"> <img   src="uploaded_img/<?= $fetch_vendor['image']; ?>" alt=""></a>
	  <div class="name"> <a  href="shop.php?vid=<?= $fetch_vendor['id']; ?>"> <?= $fetch_vendor['name']; ?>   </a></div>
	   <input type="hidden" name="vid" value="<?= $fetch_vendor['id']; ?>">
	 
   </form>
    <?php 
		 }
	   }else{
		  echo '<p class="empty">no vendor available yet!</p>';
	   }?>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
