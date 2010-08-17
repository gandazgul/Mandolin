<?php
require_once dirname(__FILE__).'/settings.php';
require_once dirname(__FILE__).'/result.php';

class SongsModel
{
	private $dbh;
	private $result;

	function __construct()
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
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->result);
	}

	//----------------------------------------------GET SONGS------------------------------------------------------------------
	function getSongs($columns, $whereCol, $whereVal)
	{
		/*echo $columns;
		echo $whereCol;
		print_r($whereVal);*/
		//prepare statement
		if (($whereCol == null) or ($whereVal == null)) {
			$sngStmt = $this->dbh->prepare("SELECT $columns FROM music ORDER BY song_name");
			$sngStmt->execute();
			$this->result->data = $sngStmt->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$sngStmt = $this->dbh->prepare("SELECT $columns FROM music WHERE $whereCol=? ORDER BY song_name");

			//cycle thru whereValues
			for ($i = 0; $i < count($whereVal); $i++){
				$val = $whereVal[$i];
				if ($val == "") continue;
				//echo $val;
				try{
					$sngStmt->execute(array($val));
					$queryArr = $sngStmt->fetchAll(PDO::FETCH_ASSOC);
					//print_r($queryArr);
					if ($queryArr === false){
						$this->dbhError();
					}else{
						$this->result->data = array_merge($this->result->data, $queryArr);
					}
				}
				catch(PDOException $e)
				{
					$this->result->isError = true;
					$this->result->errorCode = $e->getCode();
					$this->result->errorStr = $e->getMessage();
				}
			}
		}

		return $this->result;
	}

	function getAllSongs($page)
	{
		/*echo $columns;
		echo $whereCol;
		print_r($whereVal);*/
		$page = $page*500;

		//prepare statement
		try{
			$sngStmt = $this->dbh->prepare("SELECT song_id, song_name, art_name, alb_name FROM music INNER JOIN albums ON music.song_album=albums.alb_id INNER JOIN artists ON music.song_art=artists.art_id ORDER BY art_name LIMIT $page, 500");
			$sngStmt->execute();
			$this->result->data = $sngStmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e)
		{
			$this->result->isError = true;
			$this->result->errorCode = $e->getCode();
			$this->result->errorStr = $e->getMessage();
		}

		return $this->result;
	}

	private function dbhError()
	{
		$this->result->isError = true;
		$error = $this->dbh->errorInfo();
		$this->result->errorCode = $error[1];
		$this->result->errorStr = $error[2];
	}
}

$mSongs = new SongsModel();

