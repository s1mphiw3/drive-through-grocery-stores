<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$delete_id]);
   header('location:cart.php');
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$p_qty, $cart_id]);
   if ($update_qty->execute([$p_qty, $cart_id])){
   //$message[] = 'cart quantity updated';}
echo $p_qty .' '.$cart_id ; }
   else{
	   echo 'inable';
   }
}

?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    	 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">		
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css"> 

   <style>
    .prod-img{
        width:calc(100%);
        height:auto;
        max-height: 10em;
        object-fit:scale-down;
        object-position:center center
    }
	</style>
</head>

<body>
<section class="shopping-cart">

<div class="content py-3">
    <div class="card card-outline card-primary rounded-0 shadow-0">
        <div class="card-header">
            <h3 class="card-title">Cart List</h3>
        </div>
        <div class="card-body">
            <div id="cart-list">
                <div class="row">
                <?php 
                $gtotal = 0;
                $vendors =  $conn->prepare("SELECT * FROM `vendor` where id in (SELECT vendor_id from products where id in (SELECT pid FROM `cart` where user_id =?)) order by `name` asc");
                 $vendors->execute([$user_id]);
                while($vrow=$vendors->fetch(PDO::FETCH_ASSOC)){				
                ?>
                    <div class="col-12 border">
                        <span>vendor: <b><?= $vrow['name'] ?></b></span>
                    </div>
                    <div class="col-12 border p-0">
                        <?php 
                      $vtotal = 0;
					 $roducts = $conn->prepare("SELECT c.*, p.name as `name`, p.price,p.image FROM `cart` c inner join products p on c.pid = p.id where c.user_id = ? and p.vendor_id = ? order by p.name asc");
						
				     $roducts->execute([$user_id,$vrow['id']]);
						
						 while($prow = $roducts->fetch(PDO::FETCH_ASSOC)){ 
                            $total = $prow['price'] * $prow['quantity'];
                            $gtotal += $total;
                            $vtotal += $total;
                        ?>
                        <div class="d-flex align-items-center border p-2">
                            <div class="col-2 text-center">
                                <a href="view_page.php?pid=<?= $prow['pid']; ?>"><img src="<?= "uploaded_img/".$prow['image'] ?>" alt="" class="img-center prod-img border bg-gradient-gray"></a>
                            </div>
                            <div class="col-auto flex-shrink-1 flex-grow-1">
                                <h3><b><?= $prow['name'] ?></b></h3>
                                <div class="d-flex">
                                    <div class="col-auto px-0"><small class="text-muted">Price : </small></div>
                                    <div class="col-auto px-0 flex-shrink-1 flex-grow-1"><p class="m-0 pl-3"><small class="text-primary"><?= $prow['price'] ?></small></p></div>
                                </div>
								<form action="" method="POST" >
								<input type="hidden" name="cart_id" value="<?= $prow['id']; ?>">
								
                                <div class="d-flex">
                                    <div class="col-auto px-0"><small class="text-muted">qty : </small></div>
                                    <div class="col-auto">
                                        <div class="" style="width:10em">
                                            <div class="input-group input-group-sm">
                                               <input type="number" min="1" class="form-control text-center" value="<?= $prow['quantity']; ?>" class="qty" name="p_qty">
                                               <input type="submit" value="update" name="update_qty" >
                                                
                                            </div>
                                        </div>
                                    </div>

                                </div></form>
								<div class ="d-flex">
								        <div class="col-auto flex-shrink-1 flex-grow-1">
                                        <a href="cart.php?delete=<?= $prow['id']; ?>" onclick="return confirm('delete this from cart?');">  remove </a>
                                    </div>
								</div>
								
								
                            </div>
                            <div class="col-3 text-right"><?= $total ?></div>
                        </div>
                    </div>
					<?php } ?>
                    <div class="col-12 border">
                        <div class="d-flex">
                            <div class="col-9 text-right font-weight-bold text-muted">total</div>
                            <div class="col-3 text-right font-weight-bold"><?= $vtotal ?></div>
                        </div>
                    </div>
                <?php } ?>
                    <div class="col-12 border">
                        <div class="d-flex">
							<div class="col-9 h4 font-weight-bold text-right text-muted">Grand Total</div>
                            <div class="col-3 h4 font-weight-bold text-right"><?= $gtotal ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	

</div>


    <div class="clear-fix mb-2"></div>
    <div class="cart-total">
	  <a href="shop_display.php" class="option-btn">continue shopping</a><br>
      <a href="cart.php?delete_all" class="delete-btn <?= ($gtotal > 1)?'':'disabled'; ?>">delete all</a><br>
      <a href="checkout.php" class="btn <?= ($gtotal > 1)?'':'disabled'; ?>">proceed to checkout</a>
    </div>
</section>
<?php include 'footer.php'; ?>

<script src="js/script.js"></script></form>
</body>