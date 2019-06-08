<?php
	session_start();
	require_once("services/dbConn.php");
	$username = $_GET["user"];

	try {
		$stmt = $conn->prepare("SELECT * FROM account WHERE username=:username");
		$stmt->bindParam(":username", $username);
		$stmt->execute();

		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		$user = $stmt->fetchAll();
	} catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $user[0]["username"]; ?></title>
</head>
<body>
	<h1><?php echo $user[0]["username"]; ?></h1>
	<img src="profile_pics/<?php echo $user[0]["profile_pic"] ?>"/>
	<p><?php echo $user[0]["info"]; ?></p>
	<?php
		if(isset($_SESSION["user"]) && (0 == strcmp($username, $_SESSION["username"]) 
			|| 0 == strcmp($_SESSION["permission"], "ADMIN"))){
			echo '<a href="editProfile.php?user='.$user[0]["user_id"].'">Edit</a>';
		}
	?>
</body>
</html>
<?php
	$conn = NULL;
?>