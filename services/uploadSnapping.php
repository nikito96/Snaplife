<?php
	session_start();
	require_once("dbConn.php");

	$user_id = $_SESSION["user"];

	$snappings_dir = "../snappings/";
	$target_snapping = $snappings_dir . basename($_FILES["snapping"]["name"]);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_snapping,PATHINFO_EXTENSION));
	$description = $_POST["description"];
	$real_world_location = $_POST["real_world_location"];
	$tags = $_POST["tags"];

	$errors = array(
		"snapping" => array(),
		"description" => array(),
		"location" => array(),
		"tags" => array()
	);

	if (0 == strcmp($_FILES["snapping"]["name"], "")) {
		$errors["snapping"][] = "No snapping selected!";
	} else {
		$check = getimagesize($_FILES["snapping"]["tmp_name"]);

		if($check !== false) {
	        $uploadOk = 1;
	    } else {
	        $errors["snapping"][] = "File is not an image.";
	        $uploadOk = 0;
	    }

	    if (file_exists($target_snapping)) {
		    $errors["snapping"][] = "Sorry, file already exists.";
		    $uploadOk = 0;
		}

		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
		    $errors["snapping"][] = "Sorry, only JPG, JPEG & PNG files are allowed.";
		    $uploadOk = 0;
		}
	}

	if (strlen($description) > 255) {
		$errors["description"][] = "Snapping's description can not be more than 255 characters!";
	}
	if (strlen($real_world_location) > 100) {
		$errors["location"][] = "Location can not be more than 100 characters!";
	}

	if (strlen($tags) > 100) {
		$errors["tags"][] = "Tags can not be more than 100 characters!";
	}

	$count = 0;
		
	foreach ($errors as $value) {
		if(count($value) > 0){
			$count++;
		}
	}

    if ($count > 0) {
		$_SESSION["errors"] = $errors;
		header("Location: ../addSnapping.php");
	} else {
		if (move_uploaded_file($_FILES["snapping"]["tmp_name"], $target_snapping)) {
			$snapping = basename($_FILES["snapping"]["name"]);
	        try {
	        	$stmt = $conn->prepare("INSERT INTO Snapping (fk_user_id, location, description, real_world_location, tags)
	        		VALUES(:fk_user_id, :location, :description, :real_world_location, :tags)");
	        	$stmt->bindParam(":fk_user_id", $user_id);
	        	$stmt->bindParam(":location", $snapping);
	        	$stmt->bindParam(":description", $description);
	        	$stmt->bindParam(":real_world_location", $real_world_location);
	        	$stmt->bindParam(":tags", $tags);
	        	$stmt->execute();
	        	header("Location: ../snaplife.php");
	        } catch (PDOException $e) {
	        	echo "Error: " . $e->getMessage();
	        }
	    } else {
	        $errors["snapping"][] = "Sorry, there was an error uploading your file.";
	        $_SESSION["errors"] = $errors;
	        header("Location: ../addSnapping.php");
	    }
	}
	$conn = NULL;	
?>