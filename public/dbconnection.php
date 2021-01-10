<?php


$con = new mysqli("localhost","root","","product_management");

	function getConnection(){
		$db_conn=new mysqli("localhost","root","","product_management");
		if($db_conn){
			return $db_conn;
		}
		else{
			return false;
		}

		$con = mysqli_connect("localhost","root","","product_management");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
	}



	/*
	function getConnection(){
		$db_conn=new mysqli("localhost","root","","product_management");
		if($db_conn){
			return $db_conn;
		}
		else{
			return false;
		}
	}

	function getProducts($query){
		$db_conn=getConnection();
		if(!$db_conn)return false;
		
		$sql="SELECT * from product_information where product_name like '%$query%' or product_description like '%$query%' or product_category like '%$query'";
		
		$result=$db_conn->query($sql);
		$db_conn->close();
		return $result;
	}
*/

?>