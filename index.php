<?php
	session_start();
	if(isset($_SESSION["user"])){
		header("Location: snaplife.php");
	}else{
    	session_destroy();
	}
?>
<html>
	<head>
		<title>Snaplife</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="styles/index.css">
	</head>
	<body>
		<div class="container">
  			<div class="row justify-content-center">
  				<div class="col-*-* m-5">
					<a href="index.php"><img src="img\snaplife_logo.jpg" /></a>
  				</div>
  			</div>
			<div class="row justify-content-center text-center">
				<div class="col-*-* border border-primary rounded p-2">
					<form action="services/login.php" method="post">
						<div class="form-group m-5">
							<label for="email">Email:</label>
							<input type="text" class="form-control" name="email" id="email" placeholder="Enter email">
						</div>
						<div class="form-group m-5">
							<label for="password">Password:</label>
							<input type="password" class="form-control" name="password" id="password" 
							placeholder="Enter password">
						</div>
						<?php
							if (isset($_GET["badLogin"])) {
								echo '<div class="text-primary mb-2">Wrong email or password!</div>';
							}
						?>
						<input class="btn btn-primary mb-3" type="submit" name="login" value="Login">
					</form>
				</div>
			</div>
			<div class="row justify-content-center">
				<div class="col-*-* m-3">
					<a class="btn btn-primary" role="button" href="register.php">Register</a>
				</div>
			</div>
		</div>
	</body>
</html>