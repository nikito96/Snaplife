<?php
	session_start();
	require_once("dbConn.php");
	$email = $_POST["email"];
	$password = $_POST["password"];

	try {
		$stmt = $conn->prepare("SELECT user_id, username, password, permission FROM Account WHERE email = :email");
	    $stmt->bindParam(':email', $email);
	    $stmt->execute();
	    $count = $stmt->rowCount();
	    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	    $user = $stmt->fetchAll();

	    if(($count > 0) && password_verify($password, $user[0]["password"])){
	    	$_SESSION["user"] = $user[0]["user_id"];
	    	$_SESSION["username"] = $user[0]["username"];
	    	$_SESSION["permission"] = $user[0]["permission"];
	    	header("Location: ../snaplife.php");
    	}else{
    		header("Location: ../index.php?badLogin=true");
    		session_destroy();
    	}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
	$conn = NULL;
?>