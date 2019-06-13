<?php
	session_start();
	require_once("dbConn.php");

	if (!isset($_SESSION["user"]) || 0 != strcmp($_SESSION["permission"], "ADMIN")) {
		header("Location: ../snaplife.php");
	} elseif (0 == strcmp($_GET["user"], "") || 0 == strcmp($_GET["admin"], "")){
		echo '<div>Empty query</div>';
	}

	$user_id = $_GET["user"];
	$admin = $_GET["admin"];

	try {
		if (0 == strcmp($admin, "true")){
			$stmt = $conn->prepare("UPDATE account
				SET permission ='ADMIN'
					WHERE user_id = $user_id");
			$stmt->execute();
		} else {
			$stmt = $conn->prepare("UPDATE account
				SET permission = 'USER'
					WHERE user_id = $user_id");
			$stmt->execute();
		}
	} catch (PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}
	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>