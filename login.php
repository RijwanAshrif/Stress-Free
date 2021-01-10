<?php 

require_once("config.php");

if(isset($_POST['login'])){

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $sql = "SELECT * FROM users WHERE username=:username OR email=:email";
    $stmt = $db->prepare($sql);
    
    
    $params = array(
        ":username" => $username,
        ":email" => $username
    );

    $stmt->execute($params);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if($user){
        
        if(password_verify($password, $user["password"])){
            
            session_start();
            $_SESSION["user"] = $user;
            
            header("Location: public");
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login Page</title>
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
<lottie-player src="https://assets8.lottiefiles.com/packages/lf20_BhWJsn.json"  background="transparent"  speed="1"  style="width: 800px; height: 800px;"  loop  autoplay></lottie-player>
		</div>
    <div class="login-content">
        <div class="col-md-6">

       

      <form action="" method="POST">
    <img src="img/stay.png">
       <h2 class="title">Welcome</h2>
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


            <div class="input-div pass">
			<div class="i"> 
           		    	<i class="fas fa-lock"></i>
           		   </div>
				   <div class="div">
                <label for="password"></label>
                <input class="form-control" type="password" name="password" placeholder="Password" />
            </div>
			</div>
			<a href="#">Forgot Password?</a>

           <input type="submit" class="btn btn-success btn-block" name="login" value="Login" />
		   <div>
                <h5>Don't have any account?</h5>
				<a align="center" class="click-btn" href="register.php">Click Here</a>
			    </div>

        </form>
            
        </div>

        <div class="col-md-6">
            
        </div>

    </div>
    </div>
    <script type="text/javascript" src="js/main.js"></script>
</body>
</html>
