<?php include('dbconnection.php'); ?>

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

<div class="CropDataView">
<center>
<table class="table" border="1" style="border-collapse:collapse;">

<tr class="tr" style="font-size: 18px;">
  <th class="th">S.No</th>
  <th class="th">Music Name</th>
  <th class="th">Music Category</th>
  <th class="th">Music Artists</th>
  
  <th class="th">Music Quantity</th>
  <th class="th">Music Album</th>
  <th class="th">Image</th>
  <th class="th">View More</th>
</tr>
<?php
$count=1;

$sql_query="SELECT * FROM product_information ORDER BY product_id DESC";
$result = mysqli_query($con,$sql_query);
while($row = mysqli_fetch_assoc($result)) { ?>
<tr class="tr" style="font-size: 18px;">
  <td class="td" align="center"><?php echo $count; ?></td>
  <td class="td" align="center"><?php echo $row["product_name"]; ?></td>
  <td class="td" align="center"><?php echo $row["product_category"]; ?></td>
    <td class="td" align="center"><?php echo $row["product_price"]; ?></td>
  <td class="td" align="center"><?php echo $row["product_availability"]; ?></td>
    <td class="td" align="center"><?php echo $row["product_description"]; ?></td>
  <td class="td" align="center"><img src="uploads/<?php echo $row['product_image_address'];?>" alt="Not available" style="width:120px;height:120px"></td>


  <td class="td" align="center"><a href="description.php?product_id=<?php echo $row["product_id"]; ?>">View More</a></td>
<?php $count++; } ?>

</table>
</center>
</div>
