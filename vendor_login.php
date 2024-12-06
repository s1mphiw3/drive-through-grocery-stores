<?php

@include 'config.php';

session_start();

if(isset($_POST['submit'])){

   $username = $_POST['username'];
   $username = filter_var($username, FILTER_SANITIZE_STRING);
   $pass = md5($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $sql = "SELECT * FROM `vendor` WHERE username = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$username, $pass]);
   $rowCount = $stmt->rowCount();  

   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if($rowCount > 0){


   if($row['usertype'] == 'vendor'){

         $_SESSION['vendor_id'] = $row['id'];
         header('location:vendor_page.php');

      }
	  else{
         $message[] = 'no user found!';
      }
	

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
   <title>login</title>

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

   <form action="" method="POST">
      <h3>login now</h3>
      <input type="text" name="username" class="box" placeholder="enter your username" autocomplete="off" required>
      <input type="password" name="pass" class="box" placeholder="enter your password" autocomplete="off" required>
      <input type="submit" value="login now" class="btn" name="submit">
      <p>don't have an account? <a href="store_register.php">register now</a></p>
   </form>

</section>


</body>
</html>