<?php
class Settings 
{
	private $dbh;
	private $dbfilepath;

	function __construct()
	{
		$this->dbfilepath = str_replace("models", "data", dirname(__FILE__) . DIRECTORY_SEPARATOR);
		$this->dbfilepath .= "settings.json";
		//echo $this->dbfilepath;
		
		if (file_exists($this->dbfilepath) and is_file($this->dbfilepath))
		{
			for ($i = 0; $i < 2; $i++)
			{
				if (!is_writable($this->dbfilepath))
					@chmod($file, 660);
				else
					break;
			}
		}
		else
		{
			if(fclose(fopen($this->dbfilepath)) === false)
				die("<font color='red'>Settings file does not exist and I cant create it. Please create an empty file called \"settings.json\" in the \"data\" folder.</font><br>");
		}

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
