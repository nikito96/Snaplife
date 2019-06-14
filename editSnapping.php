<?php
	session_start();
	require_once("services/dbConn.php");
	if(isset($_SESSION["user"])){
		$snapping_id = $_GET["snapping"];
		$user_id = $_SESSION["user"];

		try {
			$stmt = $conn->prepare("SELECT * FROM snapping WHERE snapping_id = :snapping_id");
			$stmt->bindParam(":snapping_id", $snapping_id);
			$stmt->execute();

			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$snapping = $stmt->fetchAll();
			if(0 == strcmp($user_id, $snapping[0]["fk_user_id"]) || 0 == strcmp($_SESSION["permission"], "ADMIN")){
				$errors = array();
				if (isset($_SESSION["errors"])) {
					$errors = $_SESSION["errors"];
				}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<form action="services/edit.php" method="post">
		<input type="hidden" name="snapping_id" value="<?php echo $snapping_id; ?>">
		<label for="description">Description:</label>
		<textarea id="description" name="description"><?php echo $snapping[0]["description"]; ?></textarea>
		<?php
			if (array_key_exists("description", $errors)) {
				foreach ($errors["description"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<label for="real_world_location">Location</label>
		<input type="text" id="real_world_location" name="real_world_location" value="<?php echo $snapping[0]["real_world_location"]; ?>">
		<?php
			if (array_key_exists("real_world_location", $errors)) {
				foreach ($errors["real_world_location"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<label for="tags">Tags:</label>
		<textarea id="tags" name="tags"><?php echo $snapping[0]["tags"]; ?></textarea>
		<?php
			if (array_key_exists("tags", $errors)) {
				foreach ($errors["tags"] as $error) {
					echo '<div>'.$error.'</div>';
				}
			}
		?>
		<input type="submit" name="editSnapping" value="Edit">
	</form>
</body>
</html>
<?php
			} else {
				header("Location: snaplife.php");
			}
		} catch(PDOException $e){
		    echo "Connection failed: " . $e->getMessage();
		}
	} else {
		header("Location: index.php");
	}
	$_SESSION["errors"] = NULL;
?>