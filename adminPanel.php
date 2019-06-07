<?php
	session_start();
	if (!isset($_SESSION["user"])) {
		header("Location: index.php");
	} elseif (0 != strcmp($_SESSION["permission"], "ADMIN")) {
		header("Location: snaplife.php");
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Admin panel</title>
</head>
<body>
<?php
	
?>
</body>
</html>