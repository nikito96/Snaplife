<?php
	session_start();
	require_once("dbConn.php");

	if (!isset($_SESSION["user"])) {
		header("Location: ../index.php");
	}

	$search_q = $_POST["search_q"];

	if (0 != strcmp($search_q, "")) {
		try {
			$stmt = $conn->prepare("SELECT username, profile_pic, info FROM account WHERE username LIKE '%$search_q%'");
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
			header("Location: ../snaplife.php?emptySearch=true");
		}
	} else {
		header("Location: ../snaplife.php?emptySearch=true");
	}
?>
	</table>
</body>
</html>