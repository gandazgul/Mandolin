<?php
require_once __DIR__.'/Settings.php';

class ArtistsModel
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

	//----------------------------------------------GET ARTIRSTS------------------------------------------------------------------
	function get()
	{
		$query = $this->dbh->query("SELECT * FROM artists ORDER BY `art_name`");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		$artArr = array();
		for ($i = 0; $i < count($queryArr); $i++)
		{
			$artArr[] = array("id" => $queryArr[$i]["art_id"], "name" => $queryArr[$i]["art_name"]);
		}
		//print_r($artArr);
		return $artArr;
	}

	function get_json()
	{

		return json_encode($this->get());
	}
}

$artists = new ArtistsModel();
?>
