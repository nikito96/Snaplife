<?php
	session_start();
	require_once("dbConn.php");
	$username = $_POST["username"];
	$email = $_POST["email"];
	$password = $_POST["password"];
	$confirm_password = $_POST["confirm_password"];
	$errors = array(
		"username" => array(),
		"email" => array(),
		"password" => array(),
		"confirm_password" => array()
	);

	if($username == ""){
		$errors["username"][] = "Username should not be empty!";
	}

	if(strlen($username) < 6 || strlen($username) > 10){
		$errors["username"][] = "Nickname should be between 6 and 10 characters!";
	}

	if($password == ''){
		$errors["password"][] = "Password should not be empty!";
	}

	if (strlen($password) < 6 || strlen($password) > 20) {
		$errors["password"][] = "Password should be between 6 and 20 characters!";
	}

	if($confirm_password == ''){
		$errors["confirm_password"][] = "Confirmation password should not be empty!";
	}

	if($email == ''){
		$errors["email"][] = "Email should not be empty!";
	}

	if(strlen($email) > 30){
		$errors["email"][] = "Email shouldn't be longer than 30 characters!";
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors["email"][] = "Invalid email format";
	}

	if (strcmp($password, $confirm_password) != 0){
		$errors["confirm_password"][] = "Passwords does not match!";
	}

	try {
		$stmt = $conn->prepare("SELECT username FROM Account WHERE email = :email");
		$stmt->bindParam(":email", $email);
		$stmt->execute();
		$countEmail = $stmt->rowCount();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}

	if($countEmail > 0){
		$errors["email"][] = "This email is already registered!";
	}

	$count = 0;
		
	foreach ($errors as $value) {
		if(count($value) > 0){
			$count++;
		}
	}

	if ($count <= 0){
		try {
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
			$stmt = $conn->prepare("INSERT INTO Account (username, email, password)
			VALUES (:username, :email, :password)");
			$stmt->bindParam(":username", $username);
			$stmt->bindParam(":email", $email);
			$stmt->bindParam(":password", $hashedPassword);
			$stmt->execute();
			header("Location: ../index.php");
		} catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	} else {
		header("Location: ../register.php");
		$_SESSION["errors"] = $errors;
	}
	$conn = NULL;
?>