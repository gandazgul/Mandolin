<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'/settings.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'/result.php';

class SongsModel
{
	private $dbh;
	private $result;
	public $song_id;
	public $song_art;
	public $song_album;

	function __construct($song_id, $song_art, $song_album)
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

		if (isset ($song_id)) { $this->song_id = $song_id; }
		if (isset ($song_art)) { $this->song_art = $song_art; }
		if (isset ($song_album)) { $this->song_album = $song_album; }
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->result);
	}

	//----------------------------------------------GET SONGS------------------------------------------------------------------
	function getSongs()
	{
		if (isset ($this->song_art))
		{
			$idList = $this->song_art;
			$colName = 'song_art';
		}
		else
		if (isset ($this->song_album))
		{
			$idList = $this->song_album;
			$colName = 'song_album';
		}

		$stmt = $this->dbh->prepare("SELECT song_id, song_name FROM music WHERE $colName=? ORDER BY song_name");
		$tok = strtok($idList, "|");
		while($tok !== false)
		{
			try//try the query
			{
				if ($stmt->execute(array($tok)) === true)//if we got it
				{
					$queryArr = $stmt->fetchAll();
					//print_r($queryArr);
					for($i = 0; $i < count($queryArr); $i++)
					{
						$this->result->data[] = array("id" => $queryArr[$i]["song_id"], "name" => $queryArr[$i]["song_name"]);
					}
					$tok = strtok("|");
				}
				else
				{
					$this->dbhError();
					break;
				}
			}
			catch (PDOException $e)
			{
				$this->result->isError = true;
				$this->result->errorCode = $e->getCode();
				$this->result->errorStr = $e->getMessage();
				break;
			}
		}//from the while

		return $this->result;
	}

	private function dbhError()
	{
		$this->result->isError = true;
		$error = $this->dbh->errorInfo();
		$this->result->errorCode = $error[1];
		$this->result->errorStr = $error[2];
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
?>
