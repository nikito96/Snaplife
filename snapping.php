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
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $snapping[0]["location"]; ?></title>
	<script src="scripts/snapping.js"></script>
</head>
<body>
	<?php
		echo '<img src="snappings/'.$snapping[0]["location"].'"/>';
		echo '<div>'.$snapping[0]["date"].'</div>';
		echo '<div>'.$snapping[0]["real_world_location"].'</div>';

		if (0 == strcmp($user_id, $snapping[0]["fk_user_id"])) {
			echo '<a href="editSnapping.php?snapping='.$snapping_id.'">Edit</a>';
			echo '<button onclick="deleteSnapping('.$snapping_id.')">Delete</button>';
		}
		echo '<div>'.$snapping[0]["description"].'</div>';
	?>
</body>
</html>

<?php
	$conn = NULL;
?>