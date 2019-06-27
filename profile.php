<?php
	session_start();
	require_once("services/dbConn.php");
	$username = $_GET["user"];

	try {
		$stmt = $conn->prepare("SELECT * FROM account WHERE username=:username");
		$stmt->bindParam(":username", $username);
		$stmt->execute();

		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		$user = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
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
	    	echo '<button id="likeBtn'.$snapping_id.'" class="btn btn-primary" 
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="btn btn-primary" 
				onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping_id.')">Dislike</button>';
			echo '<style scoped>';
			echo '#dislikeBtn'.$snapping_id.' {display: none;}';
			echo '</style>';
	    } else {
	    	echo '<button id="likeBtn'.$snapping_id.'" class="btn btn-primary" 
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="btn btn-primary" 
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
	<title><?php echo $user[0]["username"]; ?></title>
	<meta charset="UTF-8"> 
	<script
			  src="https://code.jquery.com/jquery-3.4.1.js"
			  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
			  crossorigin="anonymous"></script>
	<script src="scripts/profile.js"></script>
	<link rel="stylesheet" type="text/css" href="styles/profile.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
				<a class="nav-link active" href="profile.php?user=<?php echo $_SESSION['username'] ?>">
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
	<div class="container">
		<div class="row justify-content-center m-3">
			<div class="col-md-12">
				<h1><?php echo $user[0]["username"]; ?></h1>
			</div>
			<div class="col-md-12">
				<img src="profile_pics/<?php echo $user[0]["profile_pic"] ?>"/>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div><?php echo $user[0]["info"]; ?></div>
			</div>
		</div>
		<div class="row justify-content-center">
	<?php
		if (0 == strcmp($_SESSION["permission"], "ADMIN")) {
			echo '<div class="col-md-12">';
			if (0 == strcmp($user[0]["permission"], "USER")) {
				echo '<a class="btn btn-primary m-3" 
				href="services/admin.php?admin=true&user='.$user[0]["user_id"].'">Make admin</a>';
			} else {
				echo '<a class="btn btn-primary m-3" 
				href="services/admin.php?admin=false&user='.$user[0]["user_id"].'">Remove admin</a>';
			}
			echo '</div>';
		}

		if(isset($_SESSION["user"]) && (0 == strcmp($username, $_SESSION["username"]) 
			|| 0 == strcmp($_SESSION["permission"], "ADMIN"))){
			echo '<div class="col-md-12">';
			echo '<a class="btn btn-primary m-3" href="editProfile.php?user='.$user[0]["user_id"].'">Edit</a>';
			echo '</div>';
		}
		echo '<br><br><br>';
	?>
		</div>
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div id="postList">
<?php
	require_once("services/dbConn.php");

	try{
		$stmt = $conn->prepare("SELECT * FROM snapping WHERE fk_user_id = :user_id ORDER BY snapping_id DESC LIMIT 5");
		$stmt->bindParam(":user_id", $user[0]["user_id"]);
		$stmt->execute();

		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		$snappings = $stmt->fetchAll();
		if($stmt->rowCount() > 0){
			foreach ($snappings as $snapping) {

				$stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE fk_snapping_id = :snapping_id");
				$stmt->bindParam(":snapping_id", $snapping["snapping_id"]);
				$stmt->execute();
				$likes = $stmt->fetchAll();
				
				$likes = count($likes);

				$postID = $snapping["snapping_id"];
				$stmt = $conn->prepare("SELECT username, profile_pic FROM account WHERE user_id=:user_id");
				$stmt->bindParam(":user_id", $snapping["fk_user_id"]);
				$stmt->execute();
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
				$user = $stmt->fetchAll();
				echo '<div class="row">';
				echo '<div class="col-md-12">';
				echo '<div class="row"><div class="col-md-12"><a href="profile.php?user='.$user[0]["username"].'">
				<img class="profile_pic" src="profile_pics/'.$user[0]["profile_pic"].'"/></div>
				<div class="col-md-12">'.$user[0]["username"].'</div></a></div>';
				if(strlen($snapping["real_world_location"]) > 0){
                    echo '<div class="row"><div class="col-md-12">
                    Location: '.$snapping["real_world_location"].'</div></div>';
                }
                echo '<div class="row"><div class="col-md-12">';
				echo '<a href="snapping.php?snapping='.$snapping["snapping_id"].'">
				<img class="img-fluid snapping" src="snappings/'.$snapping["location"].'"/></a>';
				echo '</div></div>';
				echo '<div class="row"><div class="col-md-12">';
				echo '<div>Uploaded on '.$snapping["date"].'</div>';
				echo '</div></div>';
				echo '<div class="row"><div class="col-md-12">';
				echo '<div>'.$snapping["description"].'</div>';
				echo '</div></div>';
				if (strlen($snapping["tags"]) > 0) {
					echo '<div class="row"><div class="col-md-12">';
					echo '<div>tags: '.$snapping["tags"].'</div>';
					echo '</div></div>';
				}
				echo '<div class="row"><div class="col-md-12">';
				buttonLikeDislike($snapping["snapping_id"], $_SESSION["user"], $conn);
				echo '</div>';
				echo '<div class="col-md-12">';
				echo '<div class="m-1" id="'."snapping".$snapping["snapping_id"].'">'.$likes.'</div>';
				echo '</div>';
				echo '<div class="col-md-12">';
				echo '<div class="m-1">likes</div>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
				echo '</div>';

			}
			echo '<div class="load-more" userID="'.$snapping["fk_user_id"].'" lastID="'.$postID.'" style="display: none;">';
			echo '<p>Loading...</p>';
			echo '</div>';
		}
	}catch(PDOException $e){
   		echo "Connection failed: " . $e->getMessage();
	}
?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<?php
	$conn = NULL;
?>