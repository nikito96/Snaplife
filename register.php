<?php
	session_start();
	if(isset($_SESSION["user"])){
		header("Location: snaplife.php");
	}
?>
<html>
<head>
	<title>Register</title>
</head>
<body>
	<?php
		$errors = array();
		
		if (isset($_SESSION["errors"])) {
			$errors = $_SESSION["errors"];
		}
	?>
	<form action="services/reg.php" method="post">
		<div>Username:</div><input type="text" name="username"><br>
		<?php
			if(array_key_exists("username", $errors)) {
				foreach ($errors["username"] as $value) {
					echo $value . "\r\n";
				}
			}
		?>
		<div>Email:</div><input type="text" name="email"><br>
		<?php
			if(array_key_exists("email", $errors)) {
				foreach ($errors["email"] as $value) {
					echo $value . "\r\n";
				}
			}
		?>
		<div>Password</div><input type="password" name="password"><br>
		<?php
			if(array_key_exists("password", $errors)) {
				foreach ($errors["password"] as $value) {
					echo $value . "\r\n";
				}
			}
		?>
		<div>Confirm password</div><input type="password" name="confirm_password"><br>
		<?php
			if(array_key_exists("confirm_password", $errors)) {
				foreach ($errors["confirm_password"] as $value) {
					echo $value . "\r\n";
				}
			}
		?>
		<input type="submit" name="register" value="Register">
	</form>
</body>
</html>

<?php
	$_SESSION["errors"] = NULL;
?>