<?php
	require_once("dbConn.php");
	$json_data = $_POST["data"];
	$data = json_decode($json_data, true);
	$liked = 0;
	try {
		$stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE 
			fk_user_id = :user_id AND fk_snapping_id = :snapping_id");
		$stmt->bindParam(":user_id", $data[1]);
		$stmt->bindParam(":snapping_id", $data[2]);
		$stmt->execute();
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	    $query = $stmt->fetchAll();

	    $liked = count($query);

	    $stmt = $conn->prepare("SELECT * FROM liked_snapping WHERE fk_snapping_id = :snapping_id");
	    $stmt->bindParam(":snapping_id", $data[2]);
		$stmt->execute();
	    $likes = $stmt->fetchAll();
	    $likes = count($likes);


	    /*if ($liked > 0) {
	    	$liked = 1;
	    } else {
	    	$liked = 0;
	    }*/

		if (1 == $data[0] && $liked == 0) {
			$stmt = $conn->prepare("INSERT INTO liked_snapping
			VALUES(:user_id, :snapping_id)");
			$stmt->bindParam(":user_id", $data[1]);
			$stmt->bindParam(":snapping_id", $data[2]);
			$stmt->execute();
			$likes++;
		} elseif (0 == $data[0] && $liked > 0) {
			$stmt = $conn->prepare("DELETE FROM liked_snapping 
				WHERE fk_user_id = :user_id AND fk_snapping_id = :snapping_id");
			$stmt->bindParam(":user_id", $data[1]);
			$stmt->bindParam(":snapping_id", $data[2]);
			$stmt->execute();
			if ($likes != 0) {
				$likes--;
			}
		}

		echo $likes;
	} catch (PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}
	$conn = NULL;
?>