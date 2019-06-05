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
	</head>
	<body>
		<form action="services/login.php" method="post">
			<label for="email">Email:</label><input type="text" name="email" id="email"><br>
			<label for="password">Password:</label><input type="password" name="password" id="password"><br>
			<?php
				if (isset($_GET["badLogin"])) {
					echo '<div>Wrong email or password!</div>';
				}
			?>
			<input type="submit" name="login" value="Login">
		</form>
		<a href="register.php">Register</a>
	</body>
</html>