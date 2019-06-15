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
			$searchResultAccounts = $stmt->fetchAll();

			$stmt = $conn->prepare("SELECT snapping.snapping_id, snapping.location, snapping.tags, account.username 
				FROM snapping
					INNER JOIN account ON snapping.fk_user_id = account.user_id
						WHERE snapping.tags LIKE '%$search_q%'");
			$stmt->execute();
			$searchResultSnappings = $stmt->fetchAll();
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
	<div>
		<table>
<?php
	$countAccounts = count($searchResultAccounts);
	if ($countAccounts > 0) {
		foreach ($searchResultAccounts as $result) {
			echo '<tr><td><img src="../profile_pics/'.$result["profile_pic"].'" /></td>
				<td><a href="../profile.php?user='.$result["username"].'">'.$result["username"].'</a></td>
				<td>'.$result["info"].'</td></tr>';
		}
	} else {
		echo '<div>No results!</div>';
	}
?>
			</table>
		</div>
		<div>
			<table>
<?php
	$countSnappings = count($searchResultSnappings);
	if ($countSnappings > 0) {
		foreach ($searchResultSnappings as $result) {
			echo '<tr><td><a href="../snapping.php?snapping='.$result["snapping_id"].'">
			<img src="../snappings/'.$result["location"].'"/></a></td>
			<td>'.$result["tags"].'</td>
			<td>'.$result["username"].'</td></tr>';
		}
	} else {
		echo '<div>No results!</div>';
	}
?>
			</table>
		</div>
<?php
	} else {
		header("Location: ../snaplife.php?emptySearch=true");
	}
?>
</body>
</html>