<?php
require('dbconnection.php');
$id=$_REQUEST['product_id'];
$query = "SELECT * from product_information where product_id='".$id."'"; 
$result = mysqli_query($con, $query) or die ( mysqli_error());
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>View More</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="form">
<?php
if(isset($_GET['new']) && $_GET['new']==1)
{
$id=$_REQUEST['product_id'];
$product_name =$_REQUEST['product_name'];
$product_category =$_REQUEST['product_category'];
$product_name =$_REQUEST['product_price'];
$product_availability =$_REQUEST['product_availability'];
$product_description =$_REQUEST['product_description'];
$product_image_address =$_REQUEST['product_image_address'];

$view="SELECT * FROM product_information WHERE product_id=".$id."";
mysqli_query($con, $view) or die(mysqli_error());
}else {
?>


<style>
body {
	background-image: linear-gradient(to right, #eaf8fe,#f8ebf8);
}
.table {
border-collapse: collapse;
width: 60%;
color: #450e83;
font-family: monospace;
font-size: 25px;
text-align: left;
}
.th {
background-color: #450e83;
color: white;
}
.tr:nth-child(even) {background-color: #f2f2f2}
.tr:nth-child(odd) {background-color: #fff}
.field6 {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}
</style>

<div class="View Product Information">
<center>
<table class="table" border="1" style="border-collapse:collapse;">

<tr class="tr" style="font-size: 18px;">
  <th class="th">Music Name</th>
  <th class="th">Music Category</th>
  <th class="th">Music Artists</th>
  
  <th class="th">Music Quantity</th>
  <th class="th">Music Album</th>
  <th class="th">Image</th>
</tr>

<tr class="tr" style="font-size: 18px;">
  <td class="td" align="center"><?php echo $row["product_name"]; ?></td>
  <td class="td" align="center"><?php 
  $product_category1 = $row["product_category"];
  echo $product_category1; ?></td>
    <td class="td" align="center"><?php echo $row["product_price"]; ?></td>
  <td class="td" align="center"><?php echo $row["product_availability"]; ?></td>
    <td class="td" align="center"><?php echo $row["product_description"]; ?></td>
  <td class="td" align="center"><img src="uploads/<?php echo $row['product_image_address'];?>" alt="Not available" style="width:120px;height:120px"></td>
</tr>
  </table>

<?php } ?>
</center>
</div>

<div class="Recomended Product">
	
	<center>
		<h2>Recomended Music</h2>
<table class="table">
<tr class="tr">
<th class="th" style="padding-left:20px;">Music Name</th>
<th class="th">Quantity</th>
</tr>
<?php
$sql = "SELECT product_name, product_price FROM product_information WHERE product_category='$product_category1'";
$result = $con->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
  	echo "<tr class='tr'><td class='td' style='padding-left:20px;'>" .  $row["product_name"] . "</td><td class='td'>". $row["product_price"] . "</td></tr>";
  }
  echo "</table></center>";
} else {
  echo "<h2>0 results</h2>";
}
$con->close();
?>
	</center>
</div>


</div>

</body>
</html>
