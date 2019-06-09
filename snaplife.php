<?php
	session_start();
	if(!isset($_SESSION["user"])){
		session_destroy();
		header("Location: index.php");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Snaplife</title>
	<meta charset="UTF-8"> 
	<script
			  src="https://code.jquery.com/jquery-3.4.1.js"
			  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
			  crossorigin="anonymous"></script>
	<script src="scripts/snaplife.js"></script>
</head>
<body>
	<a href="addSnapping.php">Add Snapping</a>
	<a href="profile.php?user=<?php echo $_SESSION['username'] ?>"><?php echo $_SESSION["username"]; ?></a>
	<a href="services/logout.php">Log out</a>
	<br>
	<br>
	<div id="postList">
<?php
	require_once("services/dbConn.php");
	try{
		$stmt = $conn->prepare("SELECT * FROM snapping ORDER BY snapping_id DESC LIMIT 2");
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
				echo '<div><a href="profile.php?user='.$user[0]["username"].'"><img src="profile_pics/'.$user[0]["profile_pic"].'"/>
				'.$user[0]["username"].'</a></div>';
				if(strlen($snapping["real_world_location"]) > 0){
                    echo "<p>Location: ".$snapping["real_world_location"]."</p>";
                }
				echo '<a href="snapping.php?snapping='.$snapping["snapping_id"].'"><img src="snappings/'.$snapping["location"].'"/></a>';
				echo '<div>Created on '.$snapping["date"].'</div>';
				echo '<p>'.$snapping["description"].'</p>';
				echo '<button class="likeBtn" onclick="likeDislike(1, '.$_SESSION["user"].', '
				.$snapping["snapping_id"].')">Like</button>
				<button class="dislikeBtn" onclick="likeDislike(0, '.$_SESSION["user"].', '
				.$snapping["snapping_id"].')">Dislike</button><div id="'."snapping".$snapping["snapping_id"].'">'.$likes.
				'</div>';
				echo '</div>';
			}
			echo '<div class="load-more" lastID="'.$postID.'" style="display: none;">';
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