<?php
require_once __DIR__.'/Settings.php';

class SongsModel
{
	private $dbh;
	private $resultArr;

	function __construct()
	{
		global $settings;

		try
		{
			$this->dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
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

	//----------------------------------------------GET SONGS------------------------------------------------------------------
	function get($alb_id)
	{
		$sngArr = array();
		$tok = strtok($alb_id, "|");
		while($tok !== false)
		{
			$query = $this->dbh->query("SELECT song_id, song_name FROM music WHERE `song_album`='$tok' ORDER BY `song_name`");
			$queryArr = $query->fetchAll();
			//print_r($queryArr);
			for($i = 0; $i < count($queryArr); $i++)
			{
				$sngArr[] = array("id" => $queryArr[$i]["song_id"], "name" => $queryArr[$i]["song_name"]);
			}
			$tok = strtok("|");
		}//from the while

		return $sngArr;
	}

	function get_json($alb_id)
	{
		return json_encode($this->get($alb_id));
	}

	/*function getInfo($song_id, $columns)
	{
		$this->resultArr['isError'] = false;

		$columns = implode(',', $columns);

		$queryArr = $this->dbh->query("SELECT $columns FROM music WHERE song_id='$song_id'");
		$queryArr = $queryArr->fetchAll();
		if (count($queryArr) == 0)
		{
			$this->resultArr['isError'] = true;
			$error = $this->dbh->errorInfo();
			$this->resultArr['resultStr'] = "ERROR: Couldn't retreive the requested information: ".$error[2];
		}
		else
		{
			$this->resultArr['resultStr'] = $queryArr;
		}

		return $this->resultArr;
	}*/

	function getInfo($songList, $columns)
	{
		$this->resultArr['isError'] = false;

		$columns = implode(',', $columns);
		
		$sngStmt = $this->dbh->prepare("SELECT $columns FROM music WHERE song_id=?");
		for ($i = 0; $i < count($songList); $i++)
		{
			$sng_id = $songList[$i];
			//echo $sng_id;
			try	
			{
				$sngStmt->execute(array($sng_id));
				$queryArr = $sngStmt->fetchAll();
				if (count($queryArr) != 0)
					$this->resultArr['resultStr'][] = $queryArr;
				else
				{
					$this->resultArr['isError'] = true;
					$this->resultArr['resultStr'] = "No results found with those parameters.";
				}
			}
			catch(PDOException $e)
			{
				$this->resultArr['isError'] = true;
				$this->resultArr['resultStr'] = $e->getMessage();
			}
		}

		//print_r($this->resultArr);
		return $this->resultArr;
	}

	function getInfo_json($songList, $columns)
	{
		return json_encode($this->getInfo($songList, $columns));
	}
}

$songs = new SongsModel();
?>
