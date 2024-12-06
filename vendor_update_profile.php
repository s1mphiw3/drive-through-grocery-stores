<?php

@include 'config.php';

session_start();

$vendor_id = $_SESSION['vendor_id'];

if(!isset($vendor_id)){
   header('location:login.php');
};

if(isset($_POST['update_profile'])){

   $name = $_POST['shop_name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $shop_type = $_POST['shop_type_id'];
   $shop_type = filter_var($shop_type, FILTER_SANITIZE_STRING);
   $shop_owner = $_POST['shop_owner'];
   $shop_owner = filter_var($shop_owner, FILTER_SANITIZE_STRING);
   $contact =  $_POST['contact'];
   $contact =  filter_var($shop_owner, FILTER_SANITIZE_STRING);
   $username =  $_POST['username'];
   $username =  filter_var($shop_owner, FILTER_SANITIZE_STRING);
   
   
   $update_profile = $conn->prepare("UPDATE `vendor` SET name = ?, shop_owner = ?,contact =?, shop_type_id =?, username =? WHERE id = ?");
   $update_profile->execute([$name, $shop_owner,$contact,$shop_type,$username,$vendor_id]);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;
   $old_image = $_POST['old_image'];

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `vendor` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $vendor_id]);
         if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/'.$old_image);
            $message[] = 'image updated successfully!';
         };
      };
   };

   $old_pass = $_POST['old_pass'];
   $update_pass = md5($_POST['update_pass']);
   $update_pass = filter_var($update_pass, FILTER_SANITIZE_STRING);
   $new_pass = md5($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = md5($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if(!empty($update_pass) AND !empty($new_pass) AND !empty($confirm_pass)){
      if($update_pass != $old_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'confirm password not matched!';
      }else{
         $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass_query->execute([$confirm_pass, $vendor_id]);
         $message[] = 'password updated successfully!';
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
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>

<body>

<?php include 'vendor_header.php'; ?>
   
<section  class="update-profile">

   <form action="" enctype="multipart/form-data" method="POST">
   
      <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
      <div class="flex">
     
	   <div class="inputBox">
	  <span>shop name :</span>
        <input type="text" id="shop_name" name="shop_name" class="box" value="<?=$fetch_profile['name']; ?>" autocomplete="off" required>
		
		<span>shop owner :</span>
        <input type="text" id="shop_owner" name="shop_owner" value="<?=$fetch_profile['shop_owner'];?>" class="box"  autocomplete="off" required>
        
		<span>owner contacts</span>
		<input type="text" id="contact" name="contact" class="box" value="<?=$fetch_profile['contact'];?>" autocomplete="off" value= "268" required>	
		
	  <span>username :</span>
	  <input type="text" autocomplete="off" id="username" name="username" class="box" value="<?=$fetch_profile['username'];?>"  required>
	   <span>update pic :</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
            <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
      </div>
	  <div class="inputBox">
	  <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
	  <span>old password :</span>
            <input type="password" name="update_pass" placeholder="enter previous password" class="box">
			
	  <span>new password  :</span>
	  <input type="password" autocomplete="off" name="new_pass" class="box" placeholder="enter your password"  required>
      
	  <span>confirm password :</span>
	  <input type="password" autocomplete="off" name="confirm_pass" class="box" placeholder="confirm your password"  required> 
	  

	 
	  <span>update shop type :</span>
	  <select type="text" id="shop_type_id" name="shop_type_id" class="box" required>
	<option value="" disabled selected></option>
	
	<?php 
	
   $sql = "SELECT * FROM `shop_type` WHERE status = ? ";
   $stmt = $conn->prepare($sql);
   $stmt->execute([1]);
   $rowCount = $stmt->rowCount();  

    if($stmt->rowCount() > 0){
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	?>
	<option value="<?=$row['id']; ?>"><?=$row['name'] ;?></option>
	<?php }
	}
    else{
      echo '<p class="empty">no store types added yet!</p>';
   }	?>      
</select></div></div>
      <div class="flex-btn">
         <input type="submit" class="btn" value="update profile" name="update_profile">
         <a href="vendor_page.php" class="option-btn">go back</a>
      </div>

</section>


<script src="js/script.js"></script>

</body>
</html>