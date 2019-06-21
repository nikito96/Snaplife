<?php
	session_start();
	if(isset($_SESSION["user"])){
		header("Location: snaplife.php");
	}
?>
<html>
<head>
	<title>Register</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
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
				<?php
					$errors = array();
					
					if (isset($_SESSION["errors"])) {
						$errors = $_SESSION["errors"];
					}
				?>
				<form action="services/reg.php" method="post">
					<div class="form-group m-3">
						<label for="username">Username:</label>
						<input class="form-control" id="username" type="text" name="username">
						<?php
							if(array_key_exists("username", $errors)) {
								foreach ($errors["username"] as $value) {
									echo '<div class="text-primary">'.$value.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group m-3">
						<label for="email">Email:</label>
						<input class="form-control" id="email" type="text" name="email">
						<?php
							if(array_key_exists("email", $errors)) {
								foreach ($errors["email"] as $value) {
									echo '<div class="text-primary">'.$value.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group m-3">
						<label for="password">Password:</label>
						<input class="form-control" id="password" type="password" name="password">
						<?php
							if(array_key_exists("password", $errors)) {
								foreach ($errors["password"] as $value) {
									echo '<div class="text-primary">'.$value.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group m-3">
						<label for="confirm_password">Confirm password:</label>
						<input class="form-control" id="confirm_password" type="password" name="confirm_password">
						<?php
							if(array_key_exists("confirm_password", $errors)) {
								foreach ($errors["confirm_password"] as $value) {
									echo '<div class="text-primary">'.$value.'</div>';
								}
							}
						?>
					</div>
					<input class="btn btn-primary m-3" type="submit" name="register" value="Register">
				</form>
			</div>
		</div>
	</div>
</body>
</html>

<?php
	$_SESSION["errors"] = NULL;
?>