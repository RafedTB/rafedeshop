<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['add_product'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_product = $conn->prepare("SELECT * FROM `products1` WHERE name = ?");
   $select_product->execute([$name]);

   if($select_product->rowCount() > 0){
      $message[] = 'product name already exist!';
   }else{
      if($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
         $insert_product = $conn->prepare("INSERT INTO `products1`(name, price, image) VALUES(?,?,?)");
         $insert_product->execute([$name, $price, $image]);
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'new product added!';
      }
   }

}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT image FROM `products1` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   $delete_product = $conn->prepare("DELETE FROM `products1` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:admin_products1.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>CURRENCY</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="add-products1">

   <h1 class="heading">add CURRENCY</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <input type="text" class="box" required maxlength="100" placeholder="currency name" name="name">
      <input type="number" step="0.0001" min="0" class="box"  max="9999999999" placeholder="enter currency price" onkeypress="if(this.value.length == 10) return false;" name="price" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="add product" class="btn" name="add_product">
   </form>

</section>

<section class="show-products1">

   <h1 class="heading">CURRENCY added</h1>

   <div class="box-container">

   <?php
      $select_products1 = $conn->prepare("SELECT * FROM `products1`");
      $select_products1->execute();
      if($select_products1->rowCount() > 0){
         while($fetch_products1 = $select_products1->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <div class="price"> TND<span><?= $fetch_products1['price']; ?></span></div>
      <img src="uploaded_img/<?= $fetch_products1['image']; ?>" alt="">
      <div class="name"><?= $fetch_products1['name']; ?></div>
      <div class="flex-btn">
         <a href="admin_product_update.php?update=<?= $fetch_products1['id']; ?>" class="option-btn">update</a>
         <a href="admin_products1.php?delete=<?= $fetch_products1['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no products1 added yet!</p>';
      }
   ?>
   
   </div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>