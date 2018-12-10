<?php

class ourConstants {
	
	/**
	 * AES Key for encryption
	 * @var String
	 */
	private $aesKey = "";
	
	/**
	 * Hash key
	 * @var String $SHA256h
	 */
	private $SHA256h = "";
	
	/**
	 * Hash key
	 * @var String
	 */
	private $SHA512h = "";
	
	/**
	 * MySql config IP/host
	 * @var String
	 */
	private $dbipadd = "";
	
	/**
	 * MySql config username
	 * @var String
	 */
	private $dbuname = "";
	
	/**
	 * MySql config password
	 * @var String
	 */
	private $dbpword = "";
	
	/**
	 * MySql config DB Name
	 * @var String
	 */
	private $dbname = "";
	
	function __construct() {
		
		$this->aesKey = "REPLACEMEWITHARANDOMKEYOFEQUALLN";
		
		//hash settings
		$this->SHA256h = '$5$rounds=5000$REPLACEMEWITHAHASHSALTKEY$';
		$this->SHA512h = '$6$rounds=5000$REPLACEMEWITHAHASHSALTKEY$';
		$this->dbipadd = "127.0.0.1";
		$this->dbuname = "TESTACCT";
		$this->dbpword = "testacct";
		$this->dbname = "BPTPOINT";
	}
	
	public function getAesKey() {
		return $this->aesKey;
	}
	
	public function getSHA256h() {
		return $this->SHA256h;	
	}
	
	public function getSHA512h() {
		return $this->SHA512h;
	}
	
	public function getDbipadd() {
		return $this->dbipadd;
	}
	
	public function getDbuname() {
		return $this->dbuname;
	}
	
	public function getDbpword() {
		return $this->dbpword;
	}
	
	public function getDbname() {
		return $this->dbname;
	}
	
	function __wakeup() {
		$this->aesKey = "REPLACEMEWITHARANDOMKEYOFEQUALLN";
		$this->SHA256h = '$5$rounds=5000$REPLACEMEWITHAHASHSALTKEY$';
		$this->SHA512h = '$6$rounds=5000$REPLACEMEWITHAHASHSALTKEY$';
		$this->dbipadd = "127.0.0.1";
		$this->dbuname = "TESTACCT";
		$this->dbpword = "testacct";
		$this->dbname = "BPTPOINT";
		
	}
	
	function __sleep() {
	
		$this->aesKey = "";
		$this->dbipadd = "";
		$this->dbuname = "";
		$this->dbpword = "";
		$this->dbname = "";
		return( array_keys( get_object_vars( $this ) ) );
	
	}
}

?>