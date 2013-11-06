<?php
class DataBaseConnection{
	public $pdo; //the actual connection
	public $result; //responses from the DB
	private $stmt; //prepared statement

	//connection info
	private $dsn;
	private $trans=FALSE; //Keep track if we're in a transaction or not
	private $auto=TRUE; //Auto-commit enabled/disabled

	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpass;
	
	/**
	*	Create PDO
	*/
	public function instantiate() {
		$this->pdo=new PDO($this->dsn,$this->dbuser,$this->dbpass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

		$this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);			  //use a persistent connection
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);	  //raise exceptions at everything
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);  //fetch associative arrays
	}

	/**
		Begin SQL transaction
			@param $auto boolean
			@public
	**/
	function beginTransaction($auto=FALSE) {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->beginTransaction();
		$this->trans=TRUE;
		$this->auto=$auto;
	}

	/**
	*	Rollback transaction and re-enable auto-commit
	*/
	public function rollbackTransaction() {
		if (!$this->pdo){
			self::instantiate();
		}
		$this->pdo->rollback();
		$this->trans=FALSE;
		$this->auto=TRUE;
	}

	/**
	*	Commit transaction and re-enable auto-commit
	*/
	function commit() {
		if (!$this->pdo)
			self::instantiate();
		$this->pdo->commit();
		$this->trans=FALSE;
		$this->auto=TRUE;
	}

	/**
	*	Run a SQL query
	*		@return array
	*		@param $cmd
	*		@param $args array
	*/
	public function exec($cmd=NULL,array $args=NULL) {
		$run_prepared_statement = (is_array($cmd) && func_num_args() == 1)
			? true
			: false;

		if ($run_prepared_statement){
			$args = $cmd; //we already have the command compiled, and so passed only one variable.  Let's keep the names straight.
			$cmd = "";
		}
		
		
		if (!$this->pdo){
			self::instantiate();
		}


		try{
			if (is_null($args)){
				$this->stmt = $this->pdo->query($cmd);
			} else {
				if (!$run_prepared_statement){ //check to see if we have a prepared a query, and if not, compile it
					$this->stmt =$this->pdo->prepare($cmd);
				}
				$this->stmt->execute($args);
			}
		}
	  	catch (PDOException $err) {
			if ($this->trans){
				$this->pdo->rollback(); //Go back to pre-transaction state	
			}
			return false;
			//throw new PDOException('PHP PDO Error: Message: "'.$err->getMessage().'" File: ' . $err->getFile().' Query: '.$query->queryString);
		 }

		//determine what to report back
		$this->result=preg_match('/^\s*(?:INSERT|UPDATE|DELETE)\s/i',$cmd)
				? $this->stmt->rowCount()
				: $this->stmt->fetchall(PDO::FETCH_ASSOC);
		
		//if ($this->stmt->rowCount() == 1){
		//	$this->result=$this->result[0]; //strip the outer array if only one row was returned
		//}
		if ($this->trans && $this->auto){$this->commit();}
		return $this->result;
	}
	
	public function prepare($cmd){
		if (!$this->pdo){
			self::instantiate();
		}
		$this->stmt = $this->pdo->prepare($cmd);
	}

	//Close connection
	function __destruct() {
		unset($this->pdo);
	}

	//Construct connection to the DB
	function __construct($dbhost,$dbname,$dbuser,$dbpass) {
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		
		if (!$this->dsn){
			$this->dsn = "mysql:host=".$dbhost.";dbname=".$dbname;
		}
		if (!$this->pdo){
			self::instantiate();
		}
	}

	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
}