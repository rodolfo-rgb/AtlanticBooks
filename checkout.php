<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['order_btn'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $method = mysqli_real_escape_string($conn, $_POST['method']);
   $address = mysqli_real_escape_string($conn, 'No. '. $_POST['flat'].', '. $_POST['street'].', '. $_POST['city'].', '. $_POST['country'].' - '. $_POST['pin_code']);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products[] = '';

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
      while($cart_item = mysqli_fetch_assoc($cart_query)){
         $cart_products[] = $cart_item['name'].' ('.$cart_item['quiantity'].') ';
         $sub_total = ($cart_item['price'] * $cart_item['quiantity']);
         $cart_total += $sub_total;
      }
   }

   $total_products = implode(', ',$cart_products);

   $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

   if($cart_total == 0){
      $message[] = 'your cart is empty';
   }else{
      if(mysqli_num_rows($order_query) > 0){
         $message[] = 'order already placed!'; 
      }else{
         mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
         $message[] = 'order placed successfully!';
         mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
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
   <title>comprar</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/styless.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>realizar compra</h3>
   <p> <a href="home.php">home</a> / checkout </p>
</div>

<section class="display-order">

   <?php  
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_cart) > 0){
         while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = ($fetch_cart['price'] * $fetch_cart['quiantity']);
            $grand_total += $total_price;
   ?>
   <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo '$'.$fetch_cart['price'].'/-'.' x '. $fetch_cart['quiantity']; ?>)</span> </p>
   <?php
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
   <div class="grand-total">total a pagar : <span>$<?php echo $grand_total; ?>/-</span> </div>

</section>

<section class="checkout">

   <form action="" method="post">
      <h3>realizar pedido</h3>
      <div class="flex">
         <div class="inputBox">
            <span>nombre :</span>
            <input type="text" name="name" required placeholder="introduce tu nombre">
         </div>
         <div class="inputBox">
            <span>número tel :</span>
            <input type="number" name="number" required placeholder="introduce tu número">
         </div>
         <div class="inputBox">
            <span>email :</span>
            <input type="email" name="email" required placeholder="introduce tu email">
         </div>
         <div class="inputBox">
            <span>metodo de pago :</span>
            <select name="method">
               <option value="efectivo">efectivo</option>
               <option value="tarjeta de credito">tarjeta de credito</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>número exterior :</span>
            <input type="number" min="0" name="flat" required placeholder="ej. 376">
         </div>
         <div class="inputBox">
            <span>direccion 2 :</span>
            <input type="text" name="street" required placeholder="ej. calle oriente 182">
         </div>
         <div class="inputBox">
            <span>cápital :</span>
            <input type="text" name="city" required placeholder="ej. México">
         </div>
         <div class="inputBox">
            <span>estado :</span>
            <input type="text" name="state" required placeholder="ej. Ciudad de México ">
         </div>
         <div class="inputBox">
            <span>país :</span>
            <input type="text" name="country" required placeholder="ej. México">
         </div>
         <div class="inputBox">
            <span>codigo pin :</span>
            <input type="number" min="0" name="pin_code" required placeholder="e.g. 123456">
         </div>
      </div>
      <input type="submit" value="ordenar" class="btn" name="order_btn">
   </form>

</section>



<?php include 'footer.php'; ?>


<script src="js/script.js"></script>

</body>
</html>