<?php
require_once dirname(__FILE__).'/settings.php';

class PlaylistsModel
{
	private $dbh;
	private $resultArr;
	private $username;
	private $plFormats;
	public $plFormatsMimeTypes;

	function __construct($username)
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

		$this->plFormats['xspf']['head'] = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">\n\t<trackList>\n";
		$this->plFormats['xspf']['track'] = "\t\t<track>\n\t\t\t<title>".'%3$d - %1$s'."</title>\n\t\t\t<location>".'%2$s'."</location>\n\t\t</track>\n";
		$this->plFormats['xspf']['foot'] = "\t</trackList>\n</playlist>";
		$this->plFormats['xspf']['amp'] = "&amp;";
		$this->plFormats['m3u']['head'] = "#EXTM3U\n";
		$this->plFormats['m3u']['track'] = '#EXTINF:0,%3$d%1$s'."\n".'%2$s'."\n";
		$this->plFormats['m3u']['foot'] = "";
		$this->plFormats['m3u']['amp'] = "&";

		$this->plFormatsMimeTypes['m3u'] = "audio/x-mpegurl";
		$this->plFormatsMimeTypes['xspf'] = "application/xspf+xml";

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
			$query = $this->dbh->query("SELECT id, pl_name FROM playlists WHERE pl_user_name='$this->username'");
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

	function get_file($plFormat, $plArr, $musicURL)
	{
		$result = array();
		$result = $this->plFormats[$plFormat]['head'];

		$sngStmt = $this->dbh->prepare("SELECT song_id, song_name FROM music WHERE song_id=?");
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
			$result .= sprintf($this->plFormats[$plFormat]['track'], $song_name, $songURL, $i + 1);
		}

		$result .= $this->plFormats[$plFormat]['foot'];

		return $result;
	}

	function post($plName, $plContent)
	{
		try
		{
			$this->dbh->exec("INSERT INTO playlists(pl_name, pl_contents, pl_user_name) VALUES ('$plName', '$plContent', '$this->username')");
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

	function delete($id)
	{
		try
		{
			$this->dbh->exec("DELETE FROM playlists WHERE id='$id' AND pl_user_name='$this->username'");
		}
		catch(PDOException $e)
		{
			echo "ERROR: Deleting playlist \"$id\"\n";
			echo $e->getMessage();
			return false;
		}
		return true;
	}

	function put($id, $data)
	{
		$columns = array_keys($data);
		$values = array_values($data);
		for ($i = 0; $i < count($data); $i++)
		{
			$setStr[] = $columns[$i]."=".$values[$i];
		}

		try
		{
			$this->dbh->exec("UPDATE playlists SET ".implode(", ", $setStr)." WHERE id=$id");
		}
		catch(PDOException $e)
		{
			echo "ERROR: Updating playlist \"$id\"\n";
			echo $e->getMessage();
			return false;
		}

		return true;
	}
}

$playlists = new PlaylistsModel($_SESSION['username']);
?>
