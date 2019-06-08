<?php
	session_start();
	require_once("services/dbConn.php");
	if(!isset($_SESSION["user"])){
		session_destroy();
		header("Location: index.php");
	}

	$user_id = $_GET["user"];

	try {
		$stmt = $conn->prepare("SELECT * FROM account WHERE user_id=:user_id");
		$stmt->bindParam(":user_id", $user_id);
		$stmt->execute();

		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		$user = $stmt->fetchAll();

		if (0 != strcmp($user_id, $user[0]["user_id"]) && 0 != strcmp($_SESSION["permission"], "admin")) {
			$user_id = $_SESSION["user"];
			$stmt->bindParam(":user_id", $user_id);
			$stmt->execute();
			$user = $stmt->fetchAll();
		}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}

	$errors = array();

	if (isset($_SESSION["errors"])) {
		$errors = $_SESSION["errors"];
	}
?>

<html>
<head>
	<title>Edit profile</title>
</head>
<body>
	<form action="services/edit.php" method="post" enctype="multipart/form-data">
		<label for="info">Profile info: </label>
		<textarea id="info" name="info"><?php echo $user[0]["info"]; ?></textarea>
		<?php
			if (array_key_exists("info", $errors)) {
				foreach ($errors["info"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<label for="profile-pic"> Profile picture: </label><input type="file" id="profile-pic" name="profile-pic">
		<?php
			if (array_key_exists("snapping", $errors)) {
				foreach ($errors["snapping"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<input type="hidden" name="user_id" value="<?php echo $user[0]["user_id"]; ?>">
		<input type="submit" name="edit" value="Edit">
	</form>
	<a href="services/edit.php?profile-pic=default">Set default profile picture</a>
	<form action="services/edit.php" method="post">
		<label for="username">Username:</label>
		<input type="text" name="username" value="<?php echo $user[0]["username"]; ?>" id="username">
		<?php
			if (array_key_exists("username", $errors)) {
				foreach ($errors["username"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<label for="password">Password:</label>
		<input type="password" name="password" id="password">
		<?php
			if (array_key_exists("password", $errors)) {
				foreach ($errors["password"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<label for="confirm_password">Confirm password:</label>
		<input type="password" name="confirm_password" id="confirm_password">
		<?php
			if (array_key_exists("confirm_password", $errors)) {
				foreach ($errors["confirm_password"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<label for="email">Email:</label>
		<input type="text" name="email" value="<?php echo $user[0]["email"] ?>" id="email">
		<?php
			if (array_key_exists("email", $errors)) {
				foreach ($errors["email"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<label for="pass_auth">Authentication password:</label><input type="password" name="pass_auth" id="pass_auth">
		<?php
			if (array_key_exists("pass_auth", $errors)) {
				foreach ($errors["pass_auth"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<input type="hidden" name="user_id" value="<?php echo $user[0]["user_id"]; ?>">
		<input type="submit" name="secured-edit" value="Edit">
	</form>
</body>
</html>
<?php
	$conn = NULL;
	$_SESSION["errors"] = NULL;
?>