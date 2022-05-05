<?php
class Database
	{
		// connection data
		private $host = "localhost";
		private $db_name = "apirest_dtb";
		private $username = "root";
		private $password = "";
		public $conn;

		// database connection
		public function connect()
		{
			$this->conn = null;
			try
				{
				$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
				$this->conn->exec("set names utf8");
				}
			catch(PDOException $exception)
				{
				echo "Errore di connessione: " . $exception->getMessage();
				}
			return $this->conn;
		}
	}
?>