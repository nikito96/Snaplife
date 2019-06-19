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
	    	echo '<button id="likeBtn'.$snapping_id.'" class="likeBtn" 
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="dislikeBtn" 
				onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping_id.')">Dislike</button>';
			echo '<style scoped>';
			echo '#dislikeBtn'.$snapping_id.' {display: none;}';
			echo '</style>';
	    } else {
	    	echo '<button id="likeBtn'.$snapping_id.'" class="likeBtn" 
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping_id.')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="dislikeBtn" 
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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> 
</head>
<body>
	<nav class="navbar navbar-expand-sm bg-primary navbar-dark fixed-top">
		<a class="navbar-brand font-weight-bold" href="snaplife.php">Snaplife</a>
		<ul class="navbar-nav">
			<li class="nav-item active">
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
	<h1><?php echo $user[0]["username"]; ?></h1>
	<img src="profile_pics/<?php echo $user[0]["profile_pic"] ?>"/>
	<p><?php echo $user[0]["info"]; ?></p>
	<?php
		if (0 == strcmp($_SESSION["permission"], "ADMIN")) {
			if (0 == strcmp($user[0]["permission"], "USER")) {
				echo '<a href="services/admin.php?admin=true&user='.$user[0]["user_id"].'">Make admin</a>';
			} else {
				echo '<a href="services/admin.php?admin=false&user='.$user[0]["user_id"].'">Remove admin</a>';
			}
		}

		if(isset($_SESSION["user"]) && (0 == strcmp($username, $_SESSION["username"]) 
			|| 0 == strcmp($_SESSION["permission"], "ADMIN"))){
			echo '<a href="editProfile.php?user='.$user[0]["user_id"].'">Edit</a>';
		}
		echo '<br><br><br>';
	?>

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
				echo '<div class="list-item">';
				if(strlen($snapping["real_world_location"]) > 0){
                    echo "<p>Location: ".$snapping["real_world_location"]."</p>";
                }
				echo '<a href="snapping.php?snapping='.$snapping["snapping_id"].'">
				<img src="snappings/'.$snapping["location"].'"/></a>';
				echo '<div>Created on '.$snapping["date"].'</div>';
				echo '<div>'.$snapping["description"].'</div>';
				if (strlen($snapping["tags"]) > 0) {
					echo '<div>tags: '.$snapping["tags"].'</div>';
				}
				buttonLikeDislike($snapping["snapping_id"], $_SESSION["user"], $conn);
				echo '<div id="'."snapping".$snapping["snapping_id"].'">'.$likes.'</div>';
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
</body>
</html>
<?php
	$conn = NULL;
?>