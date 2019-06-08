<?php
	session_start();
	require_once("dbConn.php");
	if (isset($_SESSION["user"])) {
		$snapping_id = $_GET["snapping"];
		try {
			$stmt = $conn->prepare("SELECT location, fk_user_id FROM snapping WHERE snapping_id = :snapping_id");
			$stmt->bindParam(":snapping_id", $snapping_id);
			$stmt->execute();

			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		    $snapping = $stmt->fetchAll();
		    if (0 == strcmp($snapping[0]["fk_user_id"], $_SESSION["user"]) || 
		    	0 == strcmp($_SESSION["permission"], "ADMIN")) {
			    unlink("../snappings/".$snapping[0]["location"]);

			    $stmt = $conn->prepare("DELETE FROM snapping WHERE snapping_id = :snapping_id");
				$stmt->bindParam(":snapping_id", $snapping_id);
				$stmt->execute();
				header("Location: ../snaplife.php");
			} else {
				header("Location: ../snaplife.php");
			}
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	} else {
		header("Location: ../index.php");
	}
?>