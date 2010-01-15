<?php
require_once __DIR__.'/Settings.php';

class PlaylistsModel
{
	private $dbh;
	private $resultArr;
	private $username;

	function __construct($username)
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

		$this->username = $username;
	}

	function __destruct()
	{
		unset($this->dbh);
		unset($this->resultArr);
	}

	//------------------------------------------------------------ Retrieve Playlists --------------------------------------------------------
	function get($id)
	{
		$resultArr = array();

		if (isset($id))//if an ID is provided then we list the contents of that particular PL
		{
			$plStmt = $this->dbh->prepare("SELECT pl_contents FROM playlists WHERE pl_user_name='$this->username' AND id=?");

			$pl = strtok($id, "|");
			while($pl !== false)
			{
				//print_r(array($this->username, $pl));
				try
				{
					$plStmt->execute(array($pl));
				}
				catch(PDOException $e) { exit($e->getMessage()); }

				$queryArr = $plStmt->fetchAll();
				if (count($queryArr) != 0)
				{
					$resultArr = array_merge($resultArr, explode("|", $queryArr[0]["pl_contents"], -1));
				}
				$pl = strtok("|");
			}
		}
		else//if no ID is provided we list all playlists for that user.
		{
			$query = $this->dbh->query("SELECT id, pl_name FROM playlists WHERE `pl_user_name`='$this->username'");
			$queryArr = $query->fetchAll();
			//print_r($queryArr);

			for($i = 0; $i < count($queryArr); $i++)
			{
				//echo $queryArr[$i]["pl_name"]."\n\n";
				//echo htmlentities($queryArr[$i]["pl_name"])."\n\n";
				$resultArr[] = array('id' => $queryArr[$i]["id"], "name" => htmlentities($queryArr[$i]["pl_name"]));
			}
		}
		
		return $resultArr;
	}

	function get_json($id)
	{
		return json_encode($this->get($id));
	}

	/*function renamePL($name, $newNameP)
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



	function getPLContents_json($plArr)
	{
		return json_encode($this->getPLContents($plArr));
	}

	function getPlaylist($plFormat, $plArr, $musicURL)
	{
		$result = array();
		$result = $this->plFormats[$plFormat]['head'];

		$sngStmt = $this->dbh->prepare("SELECT song_id, song_name FROM music WHERE `song_id`=?");
		for ($i = 0; $i < count($plArr); $i++)
		{
			try
			{
				$sngStmt->execute(array($plArr[$i]));
			}
			catch (PDOException $e) { exit($e->getMessage()); }

			$queryArr = $sngStmt->fetchAll();
			$song_id = $queryArr[0]['song_id'];
			$song_name = $queryArr[0]['song_name'];
			$songURL = $musicURL."server/stream.php?k=".$_SESSION["key"].$this->plFormats[$plFormat]['amp']."s=$song_id";
			//			#EXTINF:LENGTH,SONG_NAME";
			$result .= sprintf($this->plFormats[$plFormat]['track'], $song_name, $songURL, $i + 1);
		}

		$result .= $this->plFormats[$plFormat]['foot'];

		return $result;
	}*/
}

$playlists = new PlaylistsModel($_SESSION['username']);
?>
