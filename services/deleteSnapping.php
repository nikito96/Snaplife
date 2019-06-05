<?php
	session_start();
	require_once("dbConn.php");

	$snapping_id = $_GET["snapping"];
	try {
		$stmt = $conn->prepare("DELETE FROM snapping WHERE snapping_id = :snapping_id");
		$stmt->bindParam(":snapping_id", $snapping_id);
		$stmt->execute();

		$stmt = $conn->prepare("SELECT location FROM snapping WHERE snapping_id = :snapping_id");
		$stmt->bindParam(":snapping_id", $snapping_id);
		$stmt->execute();

		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	    $snapping_location = $stmt->fetchAll();
	    
	    unlink("../snappings/".$snapping_location[0]["location"]);
	} catch (PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}
?>