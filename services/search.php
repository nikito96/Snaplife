<?php
	session_start();
	require_once("dbConn.php");

	if (!isset($_SESSION["user"])) {
		header("Location: ../index.php");
	}

	$search_q = $_POST["search_q"];

	if (0 != strcmp($search_q, "")) {
		try {
			$stmt = $conn->prepare("SELECT username, profile_pic, info FROM account WHERE username LIKE '%$search_q%'");
			$stmt->execute();

			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$searchResultAccounts = $stmt->fetchAll();

			$stmt = $conn->prepare("SELECT snapping.snapping_id, snapping.location, snapping.tags, account.username 
				FROM snapping
					INNER JOIN account ON snapping.fk_user_id = account.user_id
						WHERE snapping.tags LIKE '%$search_q%'");
			$stmt->execute();
			$searchResultSnappings = $stmt->fetchAll();
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Search</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../styles/search.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-expand-sm bg-primary navbar-dark sticky-top">
		<a class="navbar-brand font-weight-bold" href="../snaplife.php">Snaplife</a>
		<form class="form-inline" action="search.php" method="POST">
			<input class="form-control mr-sm-2" type="text" name="search_q" placeholder="Search">
			<input class="btn btn-primary" type="submit" name="search" value="Search">
		</form>
		<ul class="navbar-nav">
			<!--<li class="nav-item active">
				<a class="nav-link" href="#">Active</a>
			</li>-->
			<li class="nav-item">
				<a class="nav-link" href="../profile.php?user=<?php echo $_SESSION['username'] ?>">
					<?php echo $_SESSION["username"]; ?></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="../addSnapping.php">Add Snapping</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="logout.php">Log out</a>
			</li>
			<!--<li class="nav-item">
				<a class="nav-link disabled" href="#">Disabled</a>
			</li>-->
		</ul>
	</nav>
	<div class="container-fluid">
		<div class="row justify-content-center text-center">
			<div class="col-*-*">
				<table class="m-5 border border-info">
					<tr>
						<th colspan="3">Profiles</th>
					</tr>
<?php
	$countAccounts = count($searchResultAccounts);
	if ($countAccounts > 0) {
		foreach ($searchResultAccounts as $result) {
			echo '<tr><td><img src="../profile_pics/'.$result["profile_pic"].'" /></td>
				<td><a href="../profile.php?user='.$result["username"].'">'.$result["username"].'</a></td>
				<td>'.$result["info"].'</td></tr>';
		}
	} else {
		echo '<tr><td>No results!</td></tr>';
	}
?>
				</table>
			</div>
			<div class="col-*-*">
				<table class="m-5 border border-info">
					<tr>
						<th colspan="3">Snappings:</th>
					</tr>
<?php
	$countSnappings = count($searchResultSnappings);
	if ($countSnappings > 0) {
		foreach ($searchResultSnappings as $result) {
			echo '<tr><td><a href="../snapping.php?snapping='.$result["snapping_id"].'">
			<img class="snapping" src="../snappings/'.$result["location"].'"/></a></td>
			<td>'.$result["tags"].'</td>
			<td><a href="../profile.php?user='.$result["username"].'">'.$result["username"].'</a></td></tr>';
		}
	} else {
		echo '<tr><td>No results!</td></tr>';
	}
?>
				</table>
			</div>
		</div>
	</div>
<?php
	} else {
		header("Location: ../snaplife.php?emptySearch=true");
	}
?>
</body>
</html>