<?php
	session_start();
	require_once("services/dbConn.php");

	$snapping_id = $_GET["snapping"];
	$user_id = $_SESSION["user"];

	try {
		$stmt = $conn->prepare("SELECT * FROM snapping WHERE snapping_id = :snapping_id");
		$stmt->bindParam(":snapping_id", $snapping_id);
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		$snapping = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}

	function buttonLikeDislike($snapping_id, $user_id, $snapping, $conn){
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
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping[0]["snapping_id"].')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="dislikeBtn" 
				onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping[0]["snapping_id"].')">Dislike</button>';
			echo '<style scoped>';
			echo '#dislikeBtn'.$snapping[0]["snapping_id"].' {display: none;}';
			echo '</style>';
	    } else {
	    	echo '<button id="likeBtn'.$snapping_id.'" class="likeBtn" 
	    		onclick="likeDislike(1, '.$_SESSION["user"].', '.$snapping[0]["snapping_id"].')">Like</button>';
			echo '<button id="dislikeBtn'.$snapping_id.'" class="dislikeBtn" 
				onclick="likeDislike(0, '.$_SESSION["user"].', '.$snapping[0]["snapping_id"].')">Dislike</button>';
			echo '<style scoped>';
			echo '#likeBtn'.$snapping[0]["snapping_id"].' {display: none;}';
			echo '</style>';
	    }
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $snapping[0]["location"]; ?></title>
	<script src="scripts/snapping.js"></script>
</head>
<body>
	<?php
		$stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE fk_snapping_id = :snapping_id");
		$stmt->bindParam(":snapping_id", $snapping[0]["snapping_id"]);
		$stmt->execute();
		$likes = $stmt->fetchAll();
			
		$likes = count($likes);

		echo '<img src="snappings/'.$snapping[0]["location"].'"/>';
		echo '<div>'.$snapping[0]["date"].'</div>';
		echo '<div>'.$snapping[0]["real_world_location"].'</div>';

		if (0 == strcmp($user_id, $snapping[0]["fk_user_id"]) || 
			0 == strcmp($_SESSION["permission"], "ADMIN")) {
			echo '<a href="editSnapping.php?snapping='.$snapping_id.'">Edit</a>';
			echo '<button onclick="deleteSnapping('.$snapping_id.')">Delete</button>';
		}
		echo '<div>'.$snapping[0]["description"].'</div>';
		buttonLikeDislike($snapping_id, $user_id, $snapping, $conn);
		echo '<div id="likes">'.$likes.'</div>';
	?>
</body>
</html>

<?php
	$conn = NULL;
?>