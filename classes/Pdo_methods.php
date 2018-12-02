<?php
require_once "Db_conn.php";
date_default_timezone_set('America/Detroit');
class PdoMethods extends DatabaseConn {
	private $sth;
	private $conn;
	private $db;
	private $error;
	public function selectBinded($sql, $bindings){
		$this->error = false;
		$this->db_connection();
		$this->sth = $this->conn->prepare($sql);
		$this->createBinding($bindings);
		$this->executeStatement();
		$this->conn = null;
		if(!$this->error){
			return $this->sth->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			return 'error';
		}
	}
	public function selectNotBinded($sql){
		$this->error = false;
		try{
			$this->db_connection();
			$this->sth = $this->conn->prepare($sql);
			$this->executeStatement();
		}
		catch (PDOException $Exception){
			return 'error';
		}
		
		$this->conn = null;
		if(!$this->error){
			return $this->sth->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			return 'error';
		}
	}
	public function otherBinded($sql, $bindings){
		$this->error = false;
		$this->db_connection();
		$this->sth = $this->conn->prepare($sql);
		$this->createBinding($bindings);
		$this->executeStatement();
		$this->conn = null;
		if(!$this->error){
			return 'noerror';
		}
		else{
			return 'error';
		}
	}
	
	private function db_connection(){
		$this->db = new DatabaseConn();
		$this->conn = $this->db->dbOpen();
	}
	private function createBinding($bindings){
		foreach ($bindings as $value) {
			switch($value[2]){
				case 'int' : $this->sth->bindParam($value[0],$value[1], PDO::PARAM_INT);
				case 'str' : $this->sth->bindParam($value[0],$value[1], PDO::PARAM_STR);
			}	
			
		}
	}
	private function executeStatement(){
		try{
			$this->sth->execute();
		}
		catch (PDOException $Exception){
			$error = date('F-j-Y \a\t h:i:s')." - ERROR! ".$Exception->getMessage()."\n";
			file_put_contents('../logs/pdo_errors.log', $error, FILE_APPEND);
			$this->error = true;
		}
	}
}