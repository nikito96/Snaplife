<?php
	session_start();
	require_once("dbConn.php");
	$email = $_POST["email"];
	$password = $_POST["password"];

	try {
		$stmt = $conn->prepare("SELECT account.user_id, account.username, account.password, account.permission, 
			permissions.permission 
			FROM account 
			INNER JOIN permissions ON account.permission = permissions.permission_id WHERE account.email = :email");
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
    		var_dump($user);
    		header("Location: ../index.php?badLogin=true");
    		session_destroy();
    	}
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
	$conn = NULL;
?>