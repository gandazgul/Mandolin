<?php
class Settings 
{
	private $dbh;
	private $dbfilepath;

	function __construct()
	{
		$this->dbfilepath = str_replace("models", "data/", dirname(__FILE__) . DIRECTORY_SEPARATOR);
		$this->dbfilepath .= "settings.json";
		//echo $settingsFile;
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

$settings = new Settings();

?>
