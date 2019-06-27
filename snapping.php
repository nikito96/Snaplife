<?php
	session_start();
	require_once("services/dbConn.php");

	$snapping_id = $_GET["snapping"];
	if (isset($_SESSION["user"])) {
		$user_id = $_SESSION["user"];
	}

	try {
		//$stmt = $conn->prepare("SELECT * FROM snapping WHERE snapping_id = :snapping_id");
		$stmt = $conn->prepare("SELECT snapping.*, account.username, account.profile_pic
			FROM snapping
				INNER JOIN account ON snapping.fk_user_id = account.user_id
					WHERE snapping_id = :snapping_id");
		$stmt->bindParam(":snapping_id", $snapping_id);
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		$snapping = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}

	function buttonLikeDislike($snapping_id, $user_id, $conn){
		try {
			$stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE 
				fk_user_id = :user_id AND fk_snapping_id = :snapping_id");
			$stmt->bindParam(":user_id", $user_id);
			$stmt->bindParam(":snapping_id", $snapping_id);
			$stmt->execute();
			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		    $query = $stmt->fetchAll();
	    } catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}

	    $liked = count($query);
	    if ($liked == 0) {
	    	echo '<button id="likeBtn'.$snapping_id.'" class="btn btn-primary m-1" 
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="btn btn-primary m-1" 
				onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping_id.')">Dislike</button>';
			echo '<style scoped>';
			echo '#dislikeBtn'.$snapping_id.' {display: none;}';
			echo '</style>';
	    } else {
	    	echo '<button id="likeBtn'.$snapping_id.'" class="btn btn-primary m-1" 
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="btn btn-primary m-1" 
				onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping_id.')">Dislike</button>';
			echo '<style scoped>';
			echo '#likeBtn'.$snapping_id.' {display: none;}';
			echo '</style>';
	    }
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $snapping[0]["location"]; ?></title>
	<script src="scripts/snapping.js"></script>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/snapping.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
	<?php
		if (isset($_SESSION["user"])) {
	?>
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
	<div class="container-fluid text-primary">
	<?php
		} else {
	?>
	<?php
			echo '<div class="row justify-content-center">';
  			echo '<div class="col-*-* m-3">';
			echo '<a href="index.php"><img src="img\snaplife_logo.jpg" /></a>';
  			echo '</div>';
  			echo '</div>';
		}
	?>
		<div class="row justify-content-center">
  			<div class="col-*-*">
	<?php
		$stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE fk_snapping_id = :snapping_id");
		$stmt->bindParam(":snapping_id", $snapping[0]["snapping_id"]);
		$stmt->execute();
		$likes = $stmt->fetchAll();
			
		$likes = count($likes);
		echo '<div class="row">';
		echo '<div class="col-*-*">';
		echo '<a href="profile.php?user='.$snapping[0]["username"].'">
		<img src="profile_pics/'.$snapping[0]["profile_pic"].'" />
		'.$snapping[0]["username"].'</a>';
		echo '</div>';
		echo '</div>';
		if(strlen($snapping[0]["real_world_location"]) > 0){
            echo '<div class="row"><div class="col-*-*">
                Location: '.$snapping[0]["real_world_location"].'</div></div>';
        }
		echo '<div class="row">';
		echo '<div class="col-*-*">';
		echo '<img class="snapping" src="snappings/'.$snapping[0]["location"].'"/>';
		echo '</div>';
		echo '</div>';
		echo '<div class="row">';
		echo '<div class="col-*-*">';
		echo '<div>Uploaded on '.$snapping[0]["date"].'</div>';
		echo '</div>';
		echo '</div>';
		echo '<div class="row">';
		echo '<div class="col-*-*">';
		echo '<div>'.$snapping[0]["description"].'</div>';
		echo '</div>';
		echo '</div>';
		if (strlen($snapping[0]["tags"]) > 0) {
			echo '<div class="row">';
			echo '<div class="col-*-*">';
			echo '<div>tags: '.$snapping[0]["tags"].'</div>';
			echo '</div>';
			echo '</div>';
		}
		echo '<div class="row">';
		echo '<div class="col-*-*">';
		if (isset($_SESSION["user"])) {
			buttonLikeDislike($snapping_id, $user_id, $conn);
		}
		echo '</div>';
		echo '<div class="col-*-*">';
		echo '<div class="m-1" id="likes">'.$likes.'</div>';
		echo '</div>';
		echo '<div class="col-*-*">';
		echo '<div class="m-1">likes</div>';
		echo '</div>';
		if (isset($_SESSION["user"]) && (0 == strcmp($user_id, $snapping[0]["fk_user_id"]) || 
			0 == strcmp($_SESSION["permission"], "ADMIN"))) {
			echo '<div class="col-*-*">';
			echo '<a class="btn btn-primary m-1" href="editSnapping.php?snapping='.$snapping_id.'">Edit</a>';
			echo '</div>';
			echo '<div class="col-*-*">';
			echo '<button class="btn btn-primary m-1" onclick="deleteSnapping('.$snapping_id.')">Delete</button>';
			echo '</div>';
		}
		echo '</div>';
	?>
			</div>
		</div>
	</div>
</body>
</html>

<?php
	$conn = NULL;
?>