<?php
	session_start();
	require_once("services/dbConn.php");
	if(isset($_SESSION["user"])){
		$snapping_id = $_GET["snapping"];
		$user_id = $_SESSION["user"];

		try {
			$stmt = $conn->prepare("SELECT * FROM snapping WHERE snapping_id = :snapping_id");
			$stmt->bindParam(":snapping_id", $snapping_id);
			$stmt->execute();

			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$snapping = $stmt->fetchAll();
			if(0 == strcmp($user_id, $snapping[0]["fk_user_id"]) || 0 == strcmp($_SESSION["permission"], "ADMIN")){
				$errors = array();
				if (isset($_SESSION["errors"])) {
					$errors = $_SESSION["errors"];
				}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo 'Edit '.$snapping[0]["location"]; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
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
		<div class="row justify-content-center text-center">
			<div class="col-*-* border border-primary rounded p-2 m-5">
				<form action="services/edit.php" method="post">
					<input type="hidden" name="snapping_id" value="<?php echo $snapping_id; ?>">
					<div class="form-group m-3">
						<label for="description">Description:</label>
						<textarea class="form-control" id="description" name="description"><?php echo $snapping[0]["description"]; ?></textarea>
						<?php
							if (array_key_exists("description", $errors)) {
								foreach ($errors["description"] as $error) {
									echo '<div class="text-primary">'.$error.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group m-3">
						<label for="real_world_location">Location</label>
						<input class="form-control" type="text" id="real_world_location" name="real_world_location" value="<?php echo $snapping[0]["real_world_location"]; ?>">
						<?php
							if (array_key_exists("real_world_location", $errors)) {
								foreach ($errors["real_world_location"] as $error) {
									echo '<div class="text-primary">'.$error.'</div>';
								}
							}
						?>
					</div>
					<div class="form-group m-3">
						<label for="tags">Tags:</label>
						<textarea class="form-control" id="tags" name="tags"><?php echo $snapping[0]["tags"];?></textarea>
						<?php
							if (array_key_exists("tags", $errors)) {
								foreach ($errors["tags"] as $error) {
									echo '<div class="text-primary">'.$error.'</div>';
								}
							}
						?>
					</div>
					<input class="btn btn-primary m-3" type="submit" name="editSnapping" value="Edit">
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
		} catch(PDOException $e){
		    echo "Connection failed: " . $e->getMessage();
		}
	} else {
		header("Location: index.php");
	}
	$_SESSION["errors"] = NULL;
?>