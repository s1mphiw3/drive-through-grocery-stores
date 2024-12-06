<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['order'])){
 
   $dopt = $_POST['pickopt'];
   $dopt = filter_var($dopt, FILTER_SANITIZE_STRING);
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = 'flat no. '. $_POST['flat'] .' '. $_POST['street'] .' '. $_POST['city'] .' '. $_POST['state'] .' '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);


   
   $vendors = $conn->prepare("SELECT * FROM `vendor` where id in (SELECT vendor_id from products where id in (SELECT pid FROM `cart` where user_id =?)) order by `name` asc");
   $vendors->execute([$user_id]);
   
    while($vrow=$vendors->fetch(PDO::FETCH_ASSOC)){ 

    $sql = $conn->prepare("INSERT INTO `orders` (`user_id`,`name`,`number`,`email`,`method`,`address`,`vendor_id`,`delivery_option`) VALUES(?,?,?,?,?,?,?,?)");
	$sql->execute([$user_id, $name, $number, $email, $method, $address, $vrow['id'],$dopt]);
	$oid = $conn->lastInsertId();
	
	
   $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND vendor_id = ?");
   $cart_query->execute([$user_id, $vrow['id']]);
  
        $cart_total = 0;
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $sub_total = $cart_item['price'] * $cart_item['quantity'];
         $cart_total += $sub_total;
		 
		 $pid = $cart_item['pid'] ;
         $quantity = $cart_item['quantity'] ;
         $price = $cart_item['price'] ;
		 
		$sql2 =  $conn->prepare("INSERT INTO `order_items` (`order_id`,`product_id`,`quantity`,`price`) VALUES (?,?,?,?)");
		$sql2 -> execute([$oid,$pid,$quantity,$price]);
		 
      };
  
  $upd = $conn->prepare("UPDATE `orders` set `total_price` = ? where id = ?");
  $upd -> execute([$cart_total,$oid ]);
	}
	
     $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
     $delete_cart->execute([$user_id]);
     $message[] = 'order placed successfully!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="display-orders">

   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      if($select_cart_items->rowCount() > 0){
         while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p> <?= $fetch_cart_items['name']; ?> <span>(<?= '$'.$fetch_cart_items['price'].'/- x '. $fetch_cart_items['quantity']; ?>)</span> </p>
   <?php
    }
   }else{
      echo '<p class="empty">your cart is empty!</p>';
   }
   ?>
</section>

           <section class="shopping-cart">
		   <div class="content py-3">
                    <div class="cart-total">
               
                    <p><b>Summary</b></p>

                    <?php 
                    $gtotal = 0;
                    $vendors = $conn->prepare("SELECT * FROM `vendor` where id in (SELECT vendor_id from products where id in (SELECT pid FROM `cart` where user_id = ? )) order by `name` asc");
                    $vendors->execute([$user_id]);
            	
				   while($vrow=$vendors->fetch(PDO::FETCH_ASSOC)){    
					$products = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND vendor_id = ?");
	                $products->execute([$user_id,$vrow['id']]);
					  $vtotal = 0;
					 while($prow = $products->fetch(PDO::FETCH_ASSOC)){ 
						$total = $prow['price'] * $prow['quantity'];
						$gtotal += $total;
						$vtotal += $total;
					 }
                    ?>
               
                      <p> <?= $vrow['name']?> :
                        <span> $<?= $vtotal ?></span></p><br>
                   
                    <?php } ?>
                    <div class="col-12 border">
						<p>grand total : <span>$<?= $gtotal; ?>/-</span></p>
                     </div>
					 </div>
                </div>
   </section>
<section class="checkout-orders">

   <form action="" method="POST">

      <h3>place your order</h3>

      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" placeholder="enter your name" class="box" required>
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" placeholder="enter your number" class="box" required>
         </div>
         <div class="inputBox">
            <span>your email :</span>
            <input type="email" name="email" placeholder="enter your email" class="box" required>
         </div>
         <div class="inputBox">
            <span>payment method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
		 <div class="inputBox">
            <span>pick up option :</span>
            <select name="pickopt" class="box" required>
               <option value="delivery"> delivery</option>
               <option value="drive_through"> drive-through</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="text" name="flat" placeholder="e.g. flat number" class="box" required>
         </div>
         <div class="inputBox">
            <span>address line 02 :</span>
            <input type="text" name="street" placeholder="e.g. street name" class="box" required>
         </div>
         <div class="inputBox">
            <span>city :</span>
            <input type="text" name="city" placeholder="e.g. mumbai" class="box" required>
         </div>
         <div class="inputBox">
            <span>state :</span>
            <input type="text" name="state" placeholder="e.g. maharashtra" class="box" required>
         </div>
         <div class="inputBox">
            <span>country :</span>
            <input type="text" name="country" placeholder="e.g. India" class="box" required>
         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>

</section>
<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>