<?php
class Connection {
	private $conn;
	private $servername = "localhost";
	private $username = "root";
	private $password = "";

	public function getConn()
	{
		try {
		    $this->conn = new PDO("mysql:host=$this->servername;dbname=snaplife", $this->username, $this->password);
		    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e){
		    echo "Connection failed: " . $e->getMessage();
		}
		return $this->conn;
	}

	public function closeConn()
	{
		$this->conn = NULL;
	}

	public function getUser($id)
	{
		try {
			$stmt = $this->conn->prepare('SELECT * FROM Account WHERE user_id = :id');
			$stmt->bindParam(":id", $id);
			$stmt->execute();
			$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			$user = $stmt->fetchAll();
			return $user;
		} catch (PDOException $e) {
			echo $e;
		}
	}
}
?>