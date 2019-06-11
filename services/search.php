<?php
	session_start();
	require_once("dbConn.php");

	if (!isset($_SESSION["user"])) {
		header("Location: ../index.php");
	}

	$username = $_POST["username"];

	if (0 != strcmp($username, "")) {
		try {
			$stmt = $conn->prepare("SELECT username, profile_pic, info FROM account WHERE username LIKE '%$username%'");
			$stmt->execute();

			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$searchResult = $stmt->fetchAll();
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Search</title>
</head>
<body>
	<table>
<?php
		$count = count($searchResult);
		if ($count > 0) {
			foreach ($searchResult as $result) {
				echo '<tr><td><img src="../profile_pics/'.$result["profile_pic"].'" /></td>
					<td><a href="../profile.php?user='.$result["username"].'">'.$result["username"].'</a></td>
						<td>'.$result["info"].'</td></tr>';
			}
		} else {
			echo '<div>No users found!</div>';
			echo '<a href="../snaplife.php">Back to index</a>';
		}
	} else {
		echo '<div>No users found!</div>';
		echo '<a href="../snaplife.php">Back to index</a>';
	}
?>
	</table>
</body>
</html>