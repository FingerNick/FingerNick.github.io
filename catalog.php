<?php
//start session
session_start();
//initialize session shopping cart
if (!isset($_SESSION['cart']))
{
 $_SESSION['cart']=array();
}
//look for catalog file
$catalogFile="catalog.dat";
//file is available, extract data from it
//place into $CATALOG array, with SKU as key
if (file_exists($catalogFile))
{
 $data=file($catalogFile);
 foreach ($data as $line)
 {
 $lineArray=explode(':', $line);
 $sku=trim($lineArray[0]);
 $CATALOG[$sku]['desc']=trim($lineArray[1]);
 $CATALOG[$sku]['price']=trim($lineArray[2]);
 }
}
else
{
 die("Could not find catalog file");
}
//check to see if the form has been submitted
//and which submit button was clicked
//if this is an add operation
//add to already existing quantities in shopping cart
if ($_POST['add'])
{
 foreach ($_POST['a_qty'] as $k=>$v)
 {
 //if the value is 0 or negative
 //don't bother changing the cart
 if ($v>0)
 {
 $_SESSION['cart'][$k]=$_SESSION['cart'][$k] + $v;
 }
 }
}
//if this is an update operation
//replace quantites in shopping cart with values entered
else if ($_POST['update'])
{
 foreach ($_POST['u_qty'] as $k=>$v)
 {
 //if the value is empty, 0 or negative
 //don't bother changing the cart
 if ($v!=" " && $v>=0)
 {
 $_SESSION['cart'][$k]=$v;
 }
 }
}
//if this is clear operation
//reset the session and the cart
//destroy all session data
else if ($_POST['clear'])
{
 $_SESSION=array();
 session_destroy();
 }
?>
<html lang="en">
<head>
  <link rel="stylesheet" href="style.css">
  <title>Kuya Well's Online</title>
</head>
<body>
  <div class="header">
  <div class="container">
    <div class="navbar">
      <div class="logo">
        <img src="navigation/01.jpg" width="125px">
        <img src="navigation/03.jpg" width="130px" height="70px">
      </div>

      <nav>
        <ul>
          <?php
            $navigation = array('Home', 'Product', 'About', 'Contact', 'Account'); ?>
            <ul>
            <?php foreach($navigation as $navigation){ ?>
              <li><?php echo $navigation; ?></li>
          <?php } ?>
          <input type="text" name="search" value="Search" style="width:170px; height:30px;margin-right:10px">
        </ul>
      </nav>
      <img src="navigation/03.png" width="30px" height="30px">
    </div>
  </div>
</div>
<div class="cart">
  <div class="catalog">
    <h2>Catalog</h2>
    <p>Please add items from the list below to your shopping cart.</p>
    <form action="<?$_SERVER['PHP_SELF']?>" method="post">
    <table border="0" cellspacing="10">
    <?php
    //print items from the catalog for selection
    foreach ($CATALOG as $k=>$v)
    {
     echo "<tr><td colspan=2>";
     echo "<b>".$v['desc']."</b>";
     echo "</td></tr>\n";
     echo "<tr><td>";
     echo "Price per unit: ".$CATALOG[$k]['price'];
     echo "</td><td>Quantity: ";
     echo "<input size=4 type=text name=\"a_qty[" .$k. "]\">";
     echo "</td></tr>\n";
    }
    ?>
    <tr>
    <td colspan="2">
    <div class="add-cart">
      <input id="add-items" type="submit" name="add" value="ADD ITEMS TO CART">
    </div>
    </td>
    </tr>
    </table>
  </div>
  <hr/>
  <hr/>
  <div class="shopping-cart">
    <h2>Shopping cart</h2>
    <table width="100%" border="0" cellspacing="10">
    <?php
    //initialize a variable to hold total cost
    $total=0;
    //check the shopping cart
    //if it contains values
    //look up the SKUs in the $CATALOG array
    //get the cost and calculate subtotals and totals
    if (is_array($_SESSION['cart']))
    {
     foreach ($_SESSION['cart'] as $k=>$v)
     {
     //only display items that have been selected
     //that is, quantities>0
     if ($v>0)
     {
     $subtotal=$v*$CATALOG[$k]['price'];
     $total+=$subtotal;
     echo "<tr><td>";
     echo"<b>$v unit(s) of " . $CATALOG[$k]['desc']."</b>";
     echo"</td><td>";
     echo "New quantity: <input size=4 type=text name=\"u_qty[" . $k . "]\">";
     echo "</td></tr>\n";
     echo "<tr><td>";
     echo "Price per unit: " . $CATALOG[$k]['price'];
     echo "</td><td>";
     echo "Sub-total: " . sprintf("%0.2f", $subtotal);
     echo "</td></tr>\n";
     }
     }
     }
    ?>
    <tr>
    <td><b>TOTAL</b></td>
    <td><b><?=sprintf("%.2f", $total)?></b></td>
    </tr>
    <tr>
      <td></td>
      <td><p id="vat">VAT included, where applicable</p></td>
    </tr>
    <tr>
      <td><input id="updatecart" type="submit" name="update" value="UPDATE CART"></td>
      <td><input id="clearcart" type="submit" name="clear" value="CLEAR CART"></td>
    </tr>
    <tr>
      <td>
        <input id="vouchercode" type="text" name="vcode" placeholder="Enter Voucher Code">
        <button id="voucher-apply" type="button" name="button">APPLY</button>
        <td><button id="checkout" type="button" name="proceed">PROCEED TO CHECKOUT</button></td>
     </td>
    </tr>
    </table>
  </div>
</div>
</form>
<br>
<br>


   <!------- featured products -------->
     <div class="small-container">
       <h2 class="title">Just For You</h2>
       <div class="row">
         <?php

         $dir = 'Featured Products';
         $files = scandir($dir);
         $files = array_diff($files, array('..', '.'));
         $files = array_values($files);

         shuffle($files);

         $products = array();

         for ($i = 0; $i < 6; $i++) {
           preg_match("!(.*?)\((.*?)\)!",$files[$i],$results);
           $product_name = str_replace('_',' ',$results[1]);
           $product_name = ucwords($product_name);

           $products[$product_name]['image'] = $files[$i];
           $products[$product_name]['price'] = $results[2];
           }

           foreach ($products as $product_name => $info){

             $content = "<div class='col-4'>"
             ."<img src = 'Featured Products/$info[image]'>"
             ."<h4>$product_name</h4>"
             ."<p>$info[price]</p>"
             ."</div>";

             echo $content;
             }
         ?>
       </div>

       <h2 class="title">Latest Products</h2>
       <div class="row">

         <?php

         $dir = 'Featured Products';
         $files = scandir($dir);
         $files = array_diff($files, array('..', '.'));
         $files = array_values($files);

         shuffle($files);

         $products = array();

         for ($i = 0; $i < count($files); $i++) {
           preg_match("!(.*?)\((.*?)\)!",$files[$i],$results);
           $product_name = str_replace('_',' ',$results[1]);
           $product_name = ucwords($product_name);

           $products[$product_name]['image'] = $files[$i];
           $products[$product_name]['price'] = $results[2];
           }

           foreach ($products as $product_name => $info){

             $content = "<div class='col-4'>"
             ."<img src = 'Featured Products/$info[image]'>"
             ."<h4>$product_name</h4>"
             ."<p>$info[price]</p>"
             ."</div>";

             echo $content;
             }
         ?>
       </div>
     </div>

<div class="center">
  <div class="ads">
    <p id="traffic">ADS BY TRAFFIC JUNKY</p>
  </div>
  <div class="pagination">
    <a href="#">&laquo; Previous</a>
    <a href="#" class="active">1</a>
    <a href="#">2</a>
    <a href="#">3</a>
    <a href="#">4</a>
    <a href="#">5</a>
    <a href="#">6</a>
    <a href="#">Next &raquo;</a>
  </div>

  <div class="category">
    <p>Browse More Categories +</p>
  </div>


</div>



<div class="footer">
  <div class="container">
    <div class="row">
      <div class="footer-col-1">
        <h3 style="color:orange"> Download Our App</h3>
        <p>Download App for Android and ios mobile phone.</p>
      </div>

      <div class="footer-col-2">
        <img src="navigation/01.jpg" width="125px">
        <p>Our Purpose Is To Sustainability Make the Pleasure and Benefits of Shopping Online Accessible to the Many. </p>
      </div>

      <div class="footer-col-3">
        <h3 style="color:orange">Useful Links</h3>
        <?php
        $footer01 = array('Coupons', 'Blog Post', 'Return Policy', 'Join Affiliate'); ?>
        <ul>
          <?php foreach($footer01 as $footer01){ ?>
          <li><?php echo $footer01; ?></li>
          <?php } ?>
        </ul>
      </div>

      <div class = "footer-col-4">
        <h3 style="color:orange">Follow us</h3>
        <ul>
          <?php
            $footer02 = array('Facebook', 'Twitter', 'Instagram', 'Youtube'); ?>
        <ul>
          <?php foreach($footer02 as $footer02){ ?>
          <li><?php echo $footer02; ?></li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
</body>
</html>
