<?php
	session_start();
	require_once("dbConn.php");

	//$user_id = $_SESSION["user"]; not sure if needed

	if(isset($_POST["secured-edit"])){
		$user_id = $_POST["user_id"];
		$username = $_POST["username"];
		$password = $_POST["password"];
		$confirm_password = $_POST["confirm_password"];
		$email = $_POST["email"];
		$pass_auth = $_POST["pass_auth"];

		try {
			$stmt = $conn->prepare("SELECT password FROM account WHERE user_id=:id");
			$stmt->bindParam(":id", $user_id);
			$stmt->execute();
			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$pass_cmp = $stmt->fetchAll();

			$errors = array(
				"username" => array(),
				"email" => array(),
				"password" => array(),
				"confirm_password" => array(),
				"pass_auth" => array()
			);

			if($username == ""){
			$errors["username"][] = "Username should not be empty!";
			}

			if(strlen($username) < 6 || strlen($username) > 10){
				$errors["username"][] = "Nickname should be between 6 and 10 characters!";
			}

			if($email == ''){
				$errors["email"][] = "Email should not be empty!";
			}

			if(strlen($email) > 30){
				$errors["email"][] = "Email shouldn't be longer than 30 characters!";
			}

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors["email"][] = "Invalid email format!";
			}

			if(!password_verify($pass_auth, $pass_cmp[0]["password"])
			 && strcmp($_SESSION["permission"], "ADMIN")){
				$errors["pass_auth"][] = "Wrong authentication password!";
			}

			$count = 0;
			
			foreach ($errors as $value) {
				if(count($value) > 0){
					$count++;
				}
			}

			if($count <= 0){
				$stmt = $conn->prepare("UPDATE Account
					SET username = :username, email = :email, password = :password
						WHERE user_id = :user_id");
				$stmt->bindParam(":username", $username);
				$stmt->bindParam(":email", $email);
				$stmt->bindParam(":user_id", $user_id);

				if(strlen($password) != 0 && strlen($confirm_password) != 0){
					if (strlen($password) < 6 || strlen($password) > 20) {
						$errors["password"][] = "Password should be between 6 and 20 characters!";
						$_SESSION["errors"] = $errors;
						header("Location: ../editProfile.php?user=".$user_id);
					} else {
						if (strcmp($password, $confirm_password) == 0) {
							$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
							$stmt->bindParam(":password", $hashedPassword);
							$stmt->execute();
							header("Location: ../editProfile.php?user=".$user_id);
						} else {
							$errors["confirm_password"][] = "Passwords do not match!";
							$_SESSION["errors"] = $errors;
							header("Location: ../editProfile.php?user=".$user_id);
						}
					}
				} else {
					if ((strlen($password) > 0 && strlen($confirm_password) == 0) || 
						(strlen($password) == 0 && strlen($confirm_password) > 0)) {
						$errors["confirm_password"][] = "Passwords do not match!";
						$_SESSION["errors"] = $errors;
						header("Location: ../editProfile.php?user=".$user_id);
					} else {
						$stmt->bindParam(":password", $pass_cmp[0]["password"]);
						$stmt->execute();
						header("Location: ../editProfile.php?user=".$user_id);
					}
				}
			} else{
				$_SESSION["errors"] = $errors;
				header("Location: ../editProfile.php?user=".$user_id);
			}
		} catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	} elseif(isset($_POST["edit"])) {
		$info = $_POST["info"];
		$user_id = $_POST["user_id"];

		$errors = array(
			"snapping" => array(),
			"info" => array()
		);

		if (0 != strcmp($_FILES["profile-pic"]["name"], "")) {

			$profile_pics_dir = "../profile_pics/";
			$target_profile_pic = $profile_pics_dir . basename($_FILES["profile-pic"]["name"]);
			$imageFileType = strtolower(pathinfo($target_profile_pic,PATHINFO_EXTENSION));

			$check = getimagesize($_FILES["profile-pic"]["tmp_name"]);	

			if (strlen($info) > 255) {
				$errors["info"][] = "Profile info can not be more than 255 characters!";
			}

			if($check == false) {
		        $errors["snapping"][] = "File is not an image.";
		    }

		    if (file_exists($target_profile_pic)) {
			    $errors["snapping"][] = "Sorry, file already exists.";
			}

			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
			    $errors["snapping"][] = "Sorry, only JPG, JPEG & PNG files are allowed.";
			}

			$count = 0;

			foreach ($errors as $value) {
				if(count($value) > 0){
					$count++;
				}
			}

			if ($count > 0) {
				$_SESSION["errors"] = $errors;
				header("Location: ../editProfile.php?user=".$user_id);
			} else {
				if (move_uploaded_file($_FILES["profile-pic"]["tmp_name"], $target_profile_pic)) {
					$profile_pic = basename($_FILES["profile-pic"]["name"]);
			        try {
						$stmt = $conn->prepare("UPDATE account
							SET profile_pic = :profile_pic, info = :info
								WHERE user_id = :user_id");
						$stmt->bindParam(":profile_pic", $profile_pic);
						$stmt->bindParam(":info", $info);
						$stmt->bindParam(":user_id", $user_id);
						$stmt->execute();
					} catch (PDOException $e) {
						echo "Error: " . $e->getMessage();
					}
					header("Location: ../editProfile.php?user=".$user_id);
			    } else {
			        $errors[] = "Sorry, there was an error uploading your file.";
			        header("Location: ../editProfile.php?user=".$user_id);
			    }
			}	
		} else {

			if (strlen($info) > 255) {
				$errors["info"][] = "Profile info can not be more than 255 characters!";
			}

			$count = 0;

			foreach ($errors as $value) {
				if(count($value) > 0){
					$count++;
				}
			}

			if ($count > 0) {
				$_SESSION["errors"] = $errors;
				header("Location: ../editProfile.php?user=".$user_id);
			} else {
				try {
					$stmt = $conn->prepare("UPDATE account
						SET info = :info
							WHERE user_id = :user_id");
					$stmt->bindParam(":info", $info);
					$stmt->bindParam(":user_id", $user_id);
					$stmt->execute();
				} catch (PDOException $e) {
					echo "Error: " . $e->getMessage();
				}
				header("Location: ../editProfile.php?user=".$user_id);
			}
		}
	}

	if (isset($_GET["profile-pic"])) {
		$user_id = $_GET["user"];
		if(0 == strcmp($user_id, $_SESSION["user"]) || 0 == strcmp($_SESSION["permission"], "ADMIN")){
			try {
				$stmt = $conn->prepare("SELECT profile_pic FROM account WHERE user_id = :user_id");
				$stmt->bindParam(":user_id", $user_id);
				$stmt->execute();
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		        $query = $stmt->fetchAll();
				if (0 != strcmp("default-profile-pic.png", $query[0]["profile_pic"])) {
					$stmt = $conn->prepare("UPDATE account
						SET profile_pic = 'default-profile-pic.png'");
					$stmt->execute();

		            unlink("../profile_pics/".$query[0]["profile_pic"]);
		            header("Location: ../editProfile.php?user=".$user_id);
		        } else{
		        	header("Location: ../editProfile.php?user=".$user_id);
		        }
			} catch (PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
		} else {
			header("Location: ../snaplife.php");
		}
	}

	if (isset($_POST["editSnapping"])) {
		$snapping_id = $_POST["snapping_id"];
		$description = $_POST["description"];
		$real_world_location = $_POST["real_world_location"];

		$errors = array(
			"description" => array(),
			"real_world_location" => array()
		);

		if (strlen($description) > 255) {
			$errors["description"][] = "Description can not be longer than 255 characters!";
		}

		if (strlen($real_world_location) > 100) {
			$errors["real_world_location"][] = "Location can not be longer than 100 characters!";
		}

		$count = 0;
		
		foreach ($errors as $value) {
			if(count($value) > 0){
				$count++;
			}
		}

		if ($count <= 0){
			try {
				$stmt = $conn->prepare("UPDATE snapping
					SET description=:description, real_world_location=:real_world_location
					WHERE snapping_id=:snapping_id");
				$stmt->bindParam(":description", $description);
				$stmt->bindParam(":real_world_location", $real_world_location);
				$stmt->bindParam(":snapping_id", $snapping_id);
				$stmt->execute();
			} catch (PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			header("Location: ../editSnapping.php?snapping=".$snapping_id);
		} else {
			$_SESSION["errors"] = $errors;
			header("Location: ../editSnapping.php?snapping=".$snapping_id);
		}
	}
	$conn = NULL;
?>