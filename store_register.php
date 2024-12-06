<?php

include 'config.php';

if(isset($_POST['submit'])){

   $shop_name = $_POST['shop_name'];
   $shop_name = filter_var($shop_name, FILTER_SANITIZE_STRING);
   
   $store_type = $_POST['shop_type_id'];
   $store_type = filter_var($store_type, FILTER_SANITIZE_STRING);
   
   $username = $_POST['username'];
   $username = filter_var($username, FILTER_SANITIZE_STRING);
   
   $owner_name = $_POST['shop_owner'];
   $owner_name = filter_var($owner_name, FILTER_SANITIZE_STRING);
   
   $contact = $_POST['contact'];
   $contact = filter_var($contact, FILTER_SANITIZE_STRING);
   
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   
   $pass = md5($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = md5($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select = $conn->prepare("SELECT * FROM `vendor` WHERE name = ?");
   $select->execute([$shop_name]);

   if($select->rowCount() > 0){
      $message[] = 'user email already exist!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         $insert = $conn->prepare("INSERT INTO `vendor`(name, shop_owner, contact,shop_type_id, username,password, image) VALUES(?,?,?,?,?,?,?)");
         $insert->execute([$shop_name, $owner_name, $contact,$store_type, $username, $pass, $image]);

         if($insert){
            if($image_size > 2000000){
               $message[] = 'image size is too large!';
            }else{
               move_uploaded_file($image_tmp_name, $image_folder);
               $message[] = 'registered successfully!';
               header('location:login.php');
            }
         }

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

<?php

if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}

?>
   
<section class="form-container">

   <form action="" enctype="multipart/form-data" method="POST">
      <h3>register now</h3>
        <input type="text" id="shop_name" name="shop_name" class="box" placeholder="enter shop name" autocomplete="off" required>
        <input type="text" id="shop_owner" name="shop_owner" class="box" placeholder="enter shop owner name" autocomplete="off" required>
        <input type="text" id="contact" name="contact" class="box" placeholder="enter owners contact details" autocomplete="off" value= "268" required>	
		
	  <input type="text" autocomplete="off" id="username" name="username" class="box" placeholder="enter your username"  required>
      <input type="password" autocomplete="off" name="pass" class="box" placeholder="enter your password"  required>
      <input type="password" autocomplete="off" name="cpass" class="box" placeholder="confirm your password"  required>
      <input type="file" autocomplete="off" name="image" class="box" required accept="image/jpg, image/jpeg, image/png" >
	  
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
</select>
      <input type="submit" value="register now" class="btn" name="submit">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</section>


</body>
</html>