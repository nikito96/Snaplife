<?php
	session_start();
	require_once("services/dbConn.php");
	if(!isset($_SESSION["user"])){
		session_destroy();
		header("Location: index.php");
	}
	
	$errors = array();

	if (isset($_SESSION["errors"])) {
		$errors = $_SESSION["errors"];
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Add snapping</title>
	<meta charset="UTF-8"> 
	<script
			  src="https://code.jquery.com/jquery-3.4.1.js"
			  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
			  crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
			<li class="nav-item active">
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
		<div class="row justify-content-center text-center">
			<div class="col-*-* border border-primary rounded p-3 mt-5">
				<form action="services/uploadSnapping.php" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="snapping">Snapping:</label>
						<input class="form-control-file border" type="file" id="snapping" name="snapping">
					</div>
					<?php
						if (array_key_exists("snapping", $errors)) {
							foreach ($errors["snapping"] as $error) {
								echo '<div class="text-primary mb-2">'.$error.'</div>';
							}
						}
					?>
					<div class="form-group">
						<label for="description">Description:</label>
						<textarea class="form-control" id="description" name="description"></textarea>
					</div>
					<?php
						if (array_key_exists("description", $errors)) {
							foreach ($errors["description"] as $error) {
								echo '<div class="text-primary mb-2">'.$error.'</div>';
							}
						}
					?>
					<div class="form-group">
						<label for="real_world_location">Location:</label>
						<input class="form-control" type="text" id="real_world_location" name="real_world_location">
					</div>
					<?php
						if (array_key_exists("location", $errors)) {
							foreach ($errors["location"] as $error) {
								echo '<div class="text-primary mb-2">'.$error.'</div>';
							}
						}
					?>
					<div class="form-group">
						<label for="tags">Tags (Separate with spaces):</label>
						<textarea class="form-control" id="tags" name="tags"></textarea>
					</div>
					<?php
						if (array_key_exists("tags", $errors)) {
							foreach ($errors["tags"] as $error) {
								echo '<div class="text-primary mb-2">'.$error.'</div>';
							}
						}
					?>
					<input class="btn btn-primary mb-3" type="submit" value="Upload snapping" name="upload">
				</form>
			</div>
		</div>
	</div>
</body>
</html>
<?php
	$_SESSION["errors"] = NULL;
?>