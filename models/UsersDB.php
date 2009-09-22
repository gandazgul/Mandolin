<?php
class UsersDB 
{
	private $dbfilepath;
	private $dbh;
	
	function __construct($dbfilepath)
	{
		$this->dbfilepath = $dbfilepath;
		$this->dbh = new PDO("sqlite:$dbfilepath");
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	function __destruct()
	{
		$this->dbh = null;
	}
	
	//------------------------------------------------------------ Retrieve Playlists --------------------------------------------------------
	function getPLContents($userName, $plNames)
	{
		$resultArr = array();
		$plStmt = $this->dbh->prepare("SELECT pl_contents FROM playlists WHERE `pl_user_name`=? AND `pl_name`=?");
		
		$pl = strtok($plNames, "|");
		while($pl !== false)
		{
			//print_r(array($userName, $pl));
			try
			{
				$plStmt->execute(array($userName, $pl));
			}
			catch(PDOException $e) { exit($e->getMessage()); }
			
			$queryArr = $plStmt->fetchAll();
			if (count($queryArr) != 0)
			{
				$resultArr = array_merge($resultArr, explode("|", $queryArr[0]["pl_contents"], -1));
			}		
			$pl = strtok("|");
		}
		//print_r($resultArr);
		return $resultArr;
	}
	
	function getPLsForUser_json($userName)
	{
		$resultArr = array();
			
		$query = $this->dbh->query("SELECT pl_name FROM playlists WHERE `pl_user_name`='$userName'");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		
		for($i = 0; $i < count($queryArr); $i++)
		{
			//echo $queryArr[$i]["pl_name"]."\n\n";
			//echo htmlentities($queryArr[$i]["pl_name"])."\n\n";
			$resultArr[] = htmlentities($queryArr[$i]["pl_name"]);
		}
		
		return json_encode($resultArr);
	}
	
	function renamePL($name, $newNameP)
	{
		$newName = str_replace('|', '', $newNameP);
		try
		{
			$this->dbh->exec("UPDATE playlists SET `pl_name`='$newName' WHERE `pl_name`='$name'");
		}
		catch(PDOException $e)
		{
			echo "ERROR: Renaming playlist \"$name\" to \"$newName\"\n";
			echo $e->getMessage();
			return false;
		}
		return true;
	}
	
	function deletePL($userName, $plName)
	{
		try
		{
			$this->dbh->exec("DELETE FROM playlists WHERE `pl_name`='$plName' AND `pl_user_name`='$userName'");
		}
		catch(PDOException $e)
		{
			echo "ERROR: Deleting playlist \"$plName\"\n";
			echo $e->getMessage();
			return false;
		}
		return true;
	}
	
	function updatePL($plName, $newContent, $concat)
	{
		try
		{
			if ($concat == "true")
				$this->dbh->exec("UPDATE playlists SET pl_contents=pl_contents || '$newContent' WHERE `pl_name`='$plName'");
			else
				$this->dbh->exec("UPDATE playlists SET pl_contents='$newContent' WHERE `pl_name`='$plName'");
		}
		catch(PDOException $e)
		{ 
			echo $e->getMessage();
			return false;
		}
		return true;
	}
	
	function createPlaylist($userName, $plName, $plContent)
	{
		try
		{
			$this->dbh->exec("INSERT INTO playlists(pl_name, pl_contents, pl_user_name) VALUES ('$plName', '$plContent', '$userName')");
		}
		catch(PDOException $e)
		{
			if ($e->getCode() == 23000)
				echo "Playlist \"$plName\" already exists. Please enter a different name.\n";
			else
			{
				echo "ERROR: Creating the playlist \"$plName\": \n";
				echo $e->getMessage();
			}
			return false;
		}
		return true;
	}
}
?>