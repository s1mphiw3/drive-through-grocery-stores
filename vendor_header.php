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

<header class="header">

   <div class="flex">

      <a href="admin_page.php" class="logo">Groco<span>.</span></a>

      <nav class="navbar">
         <a href="vendor_page.php">home</a>
         <a href="vendor_products.php">products</a>
         <a href="vendor_orders.php">orders</a>
         <a href="admin_contacts.php">messages</a>
      </nav>
      
	        <div class="icons"> 
	     <a class="far fa-bell" aria-hidden="true" id="noti_number" href="unread_orders.php"></a>
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `vendor` WHERE id = ?");
            $select_profile->execute([$vendor_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
         <p><?= $fetch_profile['name']; ?></p>
         <a href="vendor_update_profile.php" class="btn">update profile</a>
         <a href="logout.php" class="delete-btn">logout</a>

      </div>

   </div>

</header>

<script type="text/javascript">
 function loadDoc() {
  setInterval(function(){

   var xhttp = new XMLHttpRequest();
   xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("noti_number").innerHTML = this.responseText;
    }
   };
   xhttp.open("GET", "data.php", true);
   xhttp.send();

  },1000);


 }
 loadDoc();</script>