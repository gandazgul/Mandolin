<?php
Class MoviesDB
{
	private $dbfilepath;
	private $dbh;
	
	function __construct($dbfilepath = "./models/dbfiles/movies.db")
	{
		$this->dbfilepath = $dbfilepath;
		$this->dbh = new PDO("sqlite:$dbfilepath");
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	function __destruct()
	{
		$this->dbh = null;
	}
	
	
}