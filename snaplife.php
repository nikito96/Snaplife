<?php
	session_start();
	require_once("services/dbConn.php");
	if(!isset($_SESSION["user"])){
		session_destroy();
		header("Location: index.php");
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
	<title>Snaplife</title>
	<meta charset="UTF-8"> 
	<script
			  src="https://code.jquery.com/jquery-3.4.1.js"
			  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
			  crossorigin="anonymous"></script>
	<script src="scripts/snaplife.js"></script>
	<?php	
		if (isset($_GET["emptySearch"])) {
			echo '<script>';
			echo 'alert("No results found!")';
			echo '</script>';
		}
	?>
</head>
<body>
	<a href="addSnapping.php">Add Snapping</a>
	<a href="profile.php?user=<?php echo $_SESSION['username'] ?>"><?php echo $_SESSION["username"]; ?></a>
	<a href="services/logout.php">Log out</a>
	<form action="services/search.php" method="POST">
		<label for="search_q"></label>
		<input type="text" name="search_q" id="search_q">
		<input type="submit" name="search" value="Search">
	</form>
	<br>
	<br>
	<div id="postList">
<?php
	try{
		$stmt = $conn->prepare("SELECT * FROM snapping ORDER BY snapping_id DESC LIMIT 5");
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
				echo '<div><a href="profile.php?user='.$user[0]["username"].'">
				<img src="profile_pics/'.$user[0]["profile_pic"].'"/>'.$user[0]["username"].'</a></div>';
				if(strlen($snapping["real_world_location"]) > 0){
                    echo "<p>Location: ".$snapping["real_world_location"]."</p>";
                }
				echo '<a href="snapping.php?snapping='.$snapping["snapping_id"].'">
				<img src="snappings/'.$snapping["location"].'"/></a>';
				echo '<div>Created on '.$snapping["date"].'</div>';
				echo '<p>'.$snapping["description"].'</p>';
				if (strlen($snapping["tags"]) > 0) {
					echo '<div>tags: '.$snapping["tags"].'</div>';
				}
				buttonLikeDislike($snapping["snapping_id"], $_SESSION["user"], $conn);
				echo '<div id="'."snapping".$snapping["snapping_id"].'">'.$likes.'</div>';
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