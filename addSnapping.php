<?php
	session_start();
	require_once("services/dbConn.php");
	if(!isset($_SESSION["user"])){
		session_destroy();
		header("Location: index.php");
	}
	
	$errors = array();

	if (isset($_SESSION["errors"])) {
		$errors = $_SESSION["errors"];
	}
?>

<form action="services/uploadSnapping.php" method="post" enctype="multipart/form-data">
	<label for="snapping">Snapping:</label>
	<input type="file" id="snapping" name="snapping">
	<?php
		if (array_key_exists("snapping", $errors)) {
			foreach ($errors["snapping"] as $error) {
				echo '<div>'.$error.'</div>';
			}
		}
	?>
	<label for="description">Description:</label>
	<textarea id="description" name="description"></textarea>
	<?php
		if (array_key_exists("description", $errors)) {
			foreach ($errors["description"] as $error) {
				echo '<div>'.$error.'</div>';
			}
		}
	?>
	<label for="real_world_location">Location:</label>
	<input type="text" id="real_world_location" name="real_world_location">
	<?php
		if (array_key_exists("location", $errors)) {
			foreach ($errors["location"] as $error) {
				echo '<div>'.$error.'</div>';
			}
		}
	?>
	<label for="tags">Tags (Separate with spaces):</label>
	<textarea id="tags" name="tags"></textarea>
	<?php
		if (array_key_exists("tags", $errors)) {
			foreach ($errors["tags"] as $error) {
				echo '<div>'.$error.'</div>';
			}
		}
	?>
	<input type="submit" value="Upload snapping" name="upload">
</form>
<?php
	$_SESSION["errors"] = NULL;
?>