<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'/settings.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'/result.php';

class AlbumsModel
{
	private $dbh;
	private $result;
	public $art_id;

	function __construct($art_id)
	{
		global $settings;

		$this->result = new Result();
		
		try
		{
			//$this->dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
			$this->dbh = new PDO($settings->get("dbDSN"));
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			$this->result->isError = true;
			$this->result->errorCode = $e->getCode();
			$this->result->errorStr = $e->getMessage();
		}

		$this->art_id = $art_id;
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->result);
	}

	//----------------------------------------------GET ALBUMS------------------------------------------------------------------
	function getAlbums()
	{
		$tok = strtok($this->art_id, "|");
		while($tok !== false)
		{
			try
			{
				$query = $this->dbh->query("SELECT alb_id, alb_name FROM albums WHERE `alb_art_id`='$tok' ORDER BY `alb_name`");
				if ($query)
				{
					$queryArr = $query->fetchAll();
					//print_r($queryArr);
					$albArr = array();
					for($i = 0; $i < count($queryArr); $i++)
					{
						$albArr[] = array("id" => $queryArr[$i]["alb_id"], "name" => $queryArr[$i]["alb_name"]);
					}
					$tok = strtok("|");

					$this->result->data = $albArr;
				}
				else
				{
					$this->result->isError = true;
					$error = $this->dbh->errorInfo();
					$this->result->errorCode = $error[1];
					$this->result->errorStr = $error[2];
				}
			}
			catch (PDOException $e)
			{
				$this->result->isError = true;
				$this->result->errorCode = $e->getCode();
				$this->result->errorStr = $e->getMessage();
			}
		}//from the while

		return $this->result;
	}
}
?>
