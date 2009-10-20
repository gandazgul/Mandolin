<?php
class Settings 
{
	private $dbfilepath;
	private $dbh;
	
	function __construct($dbfilepath = "../models/dbfiles/settings.json")
	{
		$this->dbfilepath = $dbfilepath;
		$this->dbh = json_decode(file_get_contents($this->dbfilepath), true);
	}
	
	function __destruct()
	{
		$this->dbh = null;
	}
	
	function set($key, $value)
	{
		$this->dbh[$key] = $value;
		$this->commit();
	}
	
	function get($key)
	{
		return $this->dbh[$key];
	}
	
	function commit()
	{
		file_put_contents($this->dbfilepath, json_encode($this->dbh));
	}
}

?>