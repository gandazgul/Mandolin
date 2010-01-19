<?php
require_once __DIR__.'/Settings.php';

class AlbumsModel
{
	private $dbh;
	private $resultArr;

	function __construct()
	{
		global $settings;

		try
		{
			//$this->dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
			$this->dbh = new PDO($settings->get("dbDSN"));
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			die($e->getMessage());
		}

		$this->resultArr = array();
		$this->resultArr['isError'] = false;
		$this->resultArr['resultStr'] = "";
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->resultArr);
	}

	//----------------------------------------------GET ALBUMS------------------------------------------------------------------
	function get($art_id)
	{
		$albArr = array();
		$tok = strtok($art_id, "|");
		while($tok !== false)
		{
			$query = $this->dbh->query("SELECT alb_id, alb_name FROM albums WHERE `alb_art_id`='$tok' ORDER BY `alb_name`");
			$queryArr = $query->fetchAll();
			//print_r($queryArr);
			for($i = 0; $i < count($queryArr); $i++)
			{
				$albArr[] = array("id" => $queryArr[$i]["alb_id"], "name" => $queryArr[$i]["alb_name"]);
			}
			$tok = strtok("|");
		}//from the while

		return $albArr;
	}

	function get_json($art_id)
	{
		return json_encode($this->get($art_id));
	}
}

$albums = new AlbumsModel();
?>
