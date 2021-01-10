<?php

require_once("config.php");

if(isset($_POST['register'])){

    
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);


    
    $sql = "INSERT INTO users (name, username, email, password) 
            VALUES (:name, :username, :email, :password)";
    $stmt = $db->prepare($sql);

    
    $params = array(
        ":name" => $name,
        ":username" => $username,
        ":password" => $password,
        ":email" => $email
    );

    $saved = $stmt->execute($params);

    if($saved) header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Registration Page</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
	<script src="https://kit.fontawesome.com/a81368914c.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<img class="dheu" src="">
	<img class="wave" src="img/wave1.png">
<div class="container">

<div class="img">
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<lottie-player src="https://assets10.lottiefiles.com/packages/lf20_SmywXC.json"  background="transparent"  speed="1"  style="width: 400px; height: 400px;"  loop  autoplay></lottie-player>
		</div>
    <div class="login-content">
        <div class="col-md-6">

     

        <form action="" method="POST">
<img src="img/stay.png">

<h2 class="title">Registration</h2>
            <div class="form-group">
			
			<div class="input-div one">
           		   <div class="i">
           		   		<i class="fas fa-user"></i>
           		   </div>
				   <div class="div">
                <label for="name"></label>
                <input class="input" required type="text" name="name" placeholder="Name" />
            </div>
			</div>
			</div>

            <div class="form-group">
			<div class="input-div one">
           		   <div class="i">
           		   		<i class="fas fa-user"></i>
           		   </div>
				   <div class="div">
                <label for="username"></label>
				
				
                <input class="input" required  type="text" name="username" placeholder="Username" />
            </div>
			</div>
			</div>

            <div class="form-group">
			<div class="input-div one">
           		   <div class="i">
           		   		<i class="fas fa-envelope"></i>
           		   </div>
				   <div class="div">
                <label for="email"></label>
                <input class="input" required type="email" name="email" placeholder="Email" />
            </div>
			</div>
			</div>

            <div class="form-group">
              <div class="input-div pass">
			<div class="i"> 
           		    	<i class="fas fa-lock"></i>
           		   </div>
				   <div class="div">
                <label for="password"></label>
                <input class="form-control" type="password" name="password" placeholder="Password" />
            </div>
			</div>
            </div>

            <input type="submit" class="btn btn-success btn-block" name="register" value="Submit" />

        </form>
            
        </div>

        <div class="col-md-6">
     
        </div>

    </div>
</div>

</body>
</html>