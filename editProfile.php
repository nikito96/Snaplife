<?php
	session_start();
	require_once("services/dbConn.php");
	if(!isset($_SESSION["user"])){
		session_destroy();
		header("Location: index.php");
	}

	$user_id = $_GET["user"];

	if(0 == strcmp($user_id, $_SESSION["user"]) || 0 == strcmp($_SESSION["permission"], "ADMIN")){

		try {
			$stmt = $conn->prepare("SELECT * FROM account WHERE user_id=:user_id");
			$stmt->bindParam(":user_id", $user_id);
			$stmt->execute();

			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$user = $stmt->fetchAll();
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
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<link rel="icon" href="favicon.ico" type="image/gif" sizes="16x16">
</head>
<body>
	<nav class="navbar navbar-expand-sm bg-primary navbar-dark sticky-top">
		<a class="navbar-brand font-weight-bold" href="snaplife.php">Snaplife</a>
		<form class="form-inline" action="services/search.php" method="POST">
			<input class="form-control mr-sm-2" type="text" name="search_q" placeholder="Search">
			<input class="btn btn-primary" type="submit" name="search" value="Search">
		</form>
		<ul class="navbar-nav">
			<!--<li class="nav-item active">
				<a class="nav-link" href="#">Active</a>
			</li>-->
			<li class="nav-item">
				<a class="nav-link" href="profile.php?user=<?php echo $_SESSION['username'] ?>">
					<?php echo $_SESSION["username"]; ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="addSnapping.php">Add Snapping</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="services/logout.php">Log out</a>
			</li>
			<!--<li class="nav-item">
				<a class="nav-link disabled" href="#">Disabled</a>
			</li>-->
		</ul>
	</nav>
	<div class="container-fluid">
		<div class="row justify-content-center text-center mt-5">
			<div class="col-md-6">
				<form action="services/edit.php" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="info">Profile info: </label>
						<textarea class="form-control" id="info" name="info"><?php echo $user[0]["info"]; ?></textarea>
						<?php
							if (array_key_exists("info", $errors)) {
								foreach ($errors["info"] as $error) {
									echo '<div>'.$error.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group">
						<label for="profile-pic"> Profile picture: </label>
						<input class="form-control-file border" type="file" id="profile-pic" name="profile-pic">
						<a class="btn btn-primary my-4" href="services/edit.php?profile-pic=default&user=<?php echo $user[0]["user_id"]; ?>">Set default profile picture</a>
						<?php
							if (array_key_exists("snapping", $errors)) {
								foreach ($errors["snapping"] as $error) {
									echo '<div>'.$error.'</div>';
								}
							}
						?>
					</div>
					<input type="hidden" name="user_id" value="<?php echo $user[0]["user_id"]; ?>">
					<input class="btn btn-primary" type="submit" name="edit" value="Edit">
				</form>
			</div>
			<div class="col-md-6">
				<form action="services/edit.php" method="post">
					<div class="form-group">
						<label for="username">Username:</label>
						<input class="form-control" type="text" name="username" 
						value="<?php echo $user[0]["username"]; ?>" id="username">
						<?php
							if (array_key_exists("username", $errors)) {
								foreach ($errors["username"] as $error) {
									echo '<div>'.$error.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group">
						<label for="password">Password:</label>
						<input class="form-control" type="password" name="password" id="password">
						<?php
							if (array_key_exists("password", $errors)) {
								foreach ($errors["password"] as $error) {
									echo '<div>'.$error.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group">
						<label for="confirm_password">Confirm password:</label>
						<input class="form-control" type="password" name="confirm_password" id="confirm_password">
						<?php
							if (array_key_exists("confirm_password", $errors)) {
								foreach ($errors["confirm_password"] as $error) {
									echo '<div>'.$error.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group">
					<label for="email">Email:</label>
					<input class="form-control" type="text" name="email" value="<?php echo $user[0]["email"] ?>"
					 id="email">
					<?php
						if (array_key_exists("email", $errors)) {
							foreach ($errors["email"] as $error) {
								echo '<div>'.$error.'</div>';
							}
						}
					?>
					</div>
					<div class="form-group">
					<label for="pass_auth">Authentication password:</label>
					<input class="form-control" type="password" name="pass_auth" id="pass_auth">
					<?php
						if (array_key_exists("pass_auth", $errors)) {
							foreach ($errors["pass_auth"] as $error) {
								echo '<div>'.$error.'</div>';
							}
						}
					?>
					</div>
					<input type="hidden" name="user_id" value="<?php echo $user[0]["user_id"]; ?>">
					<input class="btn btn-primary" type="submit" name="secured-edit" value="Edit">
				</form>
			</div>
		</div>
	</div>
</body>
</html>
<?php
	} else {
		header("Location: snaplife.php");
	}
	$conn = NULL;
	$_SESSION["errors"] = NULL;
?>