<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_users = $conn->prepare("DELETE FROM `vendor` WHERE id = ?");
   $delete_users->execute([$delete_id]);
   header('location: vendor.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="user-accounts">

   <h1 class="title">vendor accounts</h1>

   <div class="box-container">

      <?php
         $select_users = $conn->prepare("SELECT * FROM `vendor`");
		 $select_users->execute ();
				 
         while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box" style="<?php if($fetch_users['id'] == $admin_id){ echo 'display:none'; }; ?>">
         <img src="uploaded_img/<?= $fetch_users['image']; ?>" alt="">
		 <p> vendor id : <span><?= $fetch_users['id']; ?></span></p>
         <p> name : <span><?= $fetch_users['name']; ?></span></p>
         <p> shop owner : <span><?= $fetch_users['shop_owner']; ?></span></p>
         <p> username : <span><?= $fetch_users['username']; ?></span></p>
		 <p> contact : <span><?= $fetch_users['contact']; ?></span></p>
	  <?php
         $select_shop_type = $conn->prepare("SELECT * FROM `shop_type` where id = ?");
		 $select_shop_type->execute ([$fetch_users['shop_type_id']]);
				 
         while($fetch_shptp = $select_shop_type->fetch(PDO::FETCH_ASSOC)){
      ?>	 
         <p> shop type : <span><?= $fetch_shptp ['name']; ?> </span></p>
		      <?php
      }
      ?>
         <a href="vendor.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('delete this user?');" class="delete-btn">delete</a>
      </div>
      <?php
      }
      ?>
   </div>

</section>


<script src="js/script.js"></script>

</body>
</html>