<?php
/*/TODO: add try..catch statements on every $query->fetchAll() to output errorInfo().
session_name("newMusicServer");
session_start();
//print_r($_POST);
if (!isset($_POST["SID"]) or ($_POST["SID"] != sha1(session_id())))
	header("Location: ./index.php");*/

$action = $_REQUEST["a"];

$action();//TODO catch possible error with try..catch

function art()
{
	global $action;
	$dbh = new PDO("sqlite:./db/music.db");
	$query = $dbh->query("SELECT * FROM artists ORDER BY `art_name`");
	$queryArr = $query->fetchAll();
	//print_r($queryArr);		
	$artArr = array();
	for ($i = 0; $i < count($queryArr); $i++)
	{
		$artArr[] = array("id" => $queryArr[$i]["art_id"], "name" => $queryArr[$i]["art_name"]);
	}
	echo json_encode($artArr);
	$dbh = null;
}

function alb()
{
	global $action;
	
	$artist = $_REQUEST["artist"];

	$dbh = new PDO("sqlite:./db/music.db");
	$albArr = array();	
	$tok = strtok($artist, "|");
	while($tok !== false)
	{
		$query = $dbh->query("SELECT alb_id, alb_name FROM albums WHERE `alb_art_id`='$tok' ORDER BY `alb_name`");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		for($i = 0; $i < count($queryArr); $i++)
		{
			$albArr[] = array("id" => $queryArr[$i]["alb_id"], "name" => $queryArr[$i]["alb_name"]);
		}
		$tok = strtok("|");
	}//from the while
	echo json_encode($albArr);
	$dbh = null;
}

function sng()
{
	$alb = $_REQUEST["alb"];
	
	$dbh = new PDO("sqlite:./db/music.db");
	$sngArr = array();	
	$tok = strtok($alb, "|");
	while($tok !== false)
	{
		$query = $dbh->query("SELECT song_id, song_name, song_comments FROM music WHERE `song_album`='$tok' ORDER BY `song_name`");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		for($i = 0; $i < count($queryArr); $i++)
		{
			$sngArr[] = array("id" => $queryArr[$i]["song_id"], "name" => $queryArr[$i]["song_name"], "comm" => utf8_encode($queryArr[$i]["song_comments"]));
		}
		$tok = strtok("|");
	}//from the while
	echo json_encode($sngArr);
	$dbh = null;
}

function addc()//add a comment to a track
{
	$sng = $_REQUEST["sng"];
	$com = $_REQUEST["com"];
	$dbh = new PDO("sqlite:./db/music.db");
	$query = $dbh->exec("UPDATE music SET `song_comments`='$com' WHERE `song_id`='$sng'");
	if ($query == 0)
	  echo "ERROR: Updating song entry: $sng to add comments: $com".implode(" ", $dbh->errorInfo());
	$dbh = null;
	
	sng();
}

function search()
{
	//init ------------------------------------------------------------------------------------------------------------
	$queryStr = $_REQUEST["q"];
	$dbh = new PDO("sqlite:./db/music.db");
	$resultArr = array();
	$queryArr = array();
	
	$queries = array();
	$queries[] = "SELECT art_id, art_name FROM artists WHERE `art_name`  LIKE '$queryStr%'";
	$queries[] = "SELECT alb_id, alb_name FROM albums WHERE `alb_name` LIKE '$queryStr%'";
	$queries[] = "SELECT song_id, song_name, song_comments FROM music WHERE `song_name` LIKE '$queryStr%'";
	$sections = array();
	$sections[] = "art";
	$sections[] = "alb";
	$sections[] = "sng";
	$attributes = array();
	$attributes[] = "id";
	$attributes[] = "name";
	$attributes[] = "comm";
	
	for ($i = 0; $i < 3; $i++)//go thru the 3 queries and sections
	{
		$query = $dbh->query($queries[$i]);
		$queryArr = $query->fetchAll();		
		$section = $sections[$i];
		
		if (count($queryArr) != 0)// if we found something
		{
			for ($j = 0; $j < count($queryArr); $j++)//go thru all the results
			{
				for ($k = 0; $k < count($queryArr[$j]) / 2; $k++)//go thru all the attributes in each result
				{
					$resultArr[$section][$j][$attributes[$k]] = utf8_encode($queryArr[$j][$k]);
				}
			}
		}
		else
			$resultArr[$section] = array();
	}

	//print_r($resultArr);
	echo json_encode($resultArr);
	
	$dbh = null;
}

function gett()
{
	$dbh = new PDO("sqlite:./db/music.db");
	
	$resultArr = array();
	
	$query = $dbh->query("SELECT COUNT(art_id) FROM artists");
	$queryArr = $query->fetchAll();
	$resultArr[] = $queryArr[0][0];

	$query = $dbh->query("SELECT COUNT(alb_id) FROM albums");
	$queryArr = $query->fetchAll();
	$resultArr[] = $queryArr[0][0];
	
	$query = $dbh->query("SELECT COUNT(song_id) FROM music");
	$queryArr = $query->fetchAll();
	$resultArr[] = $queryArr[0][0];
	
	echo json_encode($resultArr);
	
	$dbh = null;
}

function saved()//returns the list of playlists
{
	$userName = $_REQUEST["un"];
	$resultArr = array();
	$dbh = new PDO("sqlite:./db/users.db");

	$query = $dbh->query("SELECT pl_name FROM playlists WHERE `pl_user_name`='$userName'");
	$queryArr = $query->fetchAll();
	
	for($i = 0; $i < count($queryArr); $i++)
	{
		$resultArr[] = $queryArr[$i]["pl_name"];
	}
	
	echo json_encode($resultArr);
	
	$dbh = null;
}

function retrPL() //this function retreives the contents of the specified playlist(s)
{
	$userName = $_REQUEST["un"];
	$pl = $_REQUEST["pl"];
	//echo $plName;
	$resultArr = array();
	
	$userDBH = new PDO("sqlite:./db/users.db");
	$musicDBH = new PDO("sqlite:./db/music.db");
			
	$plName = strtok($pl, "|");
	while($plName !== false)
	{
		//echo $plName;
		$query = $userDBH->query("SELECT pl_contents FROM playlists WHERE `pl_user_name`='$userName' AND `pl_name`='$plName'");
		$queryArr = $query->fetchAll();
		$idArr = explode("|", $queryArr[0]["pl_contents"], -1);
		//print_r($idArr);
		for ($i = 0; $i < count($idArr); $i++)
		{
			$sng_id = $idArr[$i];
			$query = $musicDBH->query("SELECT song_name FROM music WHERE `song_id`='$sng_id'");
			$queryArr = $query->fetchAll();
			$resultArr[] = array("id" => $sng_id, "name" => $queryArr[0]["song_name"]);
		}

		$plName = strtok("|");
	}
	$musicDBH = null;
	$userDBH = null;
	
	echo json_encode($resultArr);
}

//-----------------------------

function delete()//deletes a playlist
{
	global $userName;

	$name = $_GET["pl"];
	echo $name;
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->exec("DELETE FROM playlists WHERE `pl_name`='$name' AND `pl_user_name`='$userName'");
	if ($query == 0)
	  echo "ERROR: Deleting playlist: \"$name\" from user: \"$userName\". Error Info: ".implode(" ", $dbh->errorInfo());
	$dbh = null;
	
	saved();
}

function createPL()//creates a playlist
{
	global $userName;
	
	$name = $_GET["name"];
	$sng = $_GET["sng"];
	//echo "$name, $sng, $userName";
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->exec("INSERT INTO playlists(pl_name, pl_contents, pl_user_name) VALUES ('$name', '$sng', '$userName')");
	if ($query == 0)
	  echo "ERROR: Inserting new playlist: $name from user: $userName with this content: $sng<br/>Error Info: ".implode(" ", $dbh->errorInfo());
	$dbh = null;

	echo "Playlist: \"$name\" was created successfuly, switch to the \"Saved Playlists\" tab to play or edit it.";
}

function download()//downloads a saved playlist TODO: make playselected look like this one. TODO: when creating a new PL make sure the name is not in use already
{
	global $userName, $cur_key;
	
	$name = $_GET["pl"];
		
	$fset = fopen("./settings", "rt");
		$musicURL = fgets($fset);
	fclose($fset);
	$musicURL = substr($musicURL, strpos($musicURL, "=") + 1, -1);	
	if (substr($musicURL, -1) != "/")
		$musicURL .= "/";
	
	header("Content-type: audio/m3u");
	header("Content-Disposition: filename=\"$name.m3u\"");
	header("Content-Transfer-Encoding: plain");
	echo "#EXTM3U\n";
	
	//this part is the only difference with play()
	$dbh = new PDO("sqlite:./db/users.db");
		$query = $dbh->query("SELECT pl_contents FROM playlists WHERE `pl_user_name`='$userName' AND `pl_name`='$name'");
		$queryArr = $query->fetchAll();
	$dbh = null;
	//this part is the only difference with play()
	
	$listContents = "'".substr(implode("','", explode("|", $queryArr[0][0])), 0, -2);
	
	$dbh = new PDO("sqlite:./db/music.db");
		$query = $dbh->query("SELECT song_id, song_name FROM music WHERE `song_id` in($listContents)");
		$queryArr = $query->fetchAll();
	$dbh = null;
	
	for($i = 0; $i < count($queryArr); $i++)
	{
		$ext = substr($queryArr[$i][1], strrpos($queryArr[$i][1], "."));
		$name = substr($queryArr[$i][1], 0, strrpos($queryArr[$i][1], "."));
		echo "#EXTINF:0,$name\n";
		echo $musicURL."stream.php?k=$cur_key&s=".$queryArr[$i][0]."&$ext\n";
	}
}

function play()//makes a list of the tracks selected in the sng list
{
	global $cur_key;
	
	$sng = $_GET["sng"];
	
	$fset = fopen("./settings", "rt");
		$musicURL = fgets($fset);
	fclose($fset);
	$musicURL = substr($musicURL, strpos($musicURL, "=") + 1, -1);	
	if (substr($musicURL, -1) != "/")
		$musicURL .= "/";

	header("Content-type: audio/m3u");
	header("Content-Disposition: filename=\"playlist.m3u\"");
	header("Content-Transfer-Encoding: plain");
	echo "#EXTM3U\n";

	$listContents = "'".substr(implode("','", explode("|", $sng)), 0, -2);

	$dbh = new PDO("sqlite:./db/music.db");
		$query = $dbh->query("SELECT song_id, song_name FROM music WHERE `song_id` in($listContents)");
		$queryArr = $query->fetchAll();
	$dbh = null;
	
	for($i = 0; $i < count($queryArr); $i++)
	{
		$ext = substr($queryArr[$i][1], strrpos($queryArr[$i][1], "."));
		$name = substr($queryArr[$i][1], 0, strrpos($queryArr[$i][1], "."));
		echo "#EXTINF:0,$name\n";
		echo $musicURL."stream.php?k=$cur_key&s=".$queryArr[$i][0]."&$ext\n";
	}
}

function ren()//rename a saved playlist
{
	$new_name = $_GET["new_name"];
	$name = $_GET["pl"];
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->exec("UPDATE playlists SET `pl_name`='$new_name' WHERE `pl_name`='$name'");
	if ($query == 0)
	  echo "ERROR: Updating playlist entry: \"$name\" to rename it to: \"$new_name\". Error Info: ".implode(" ", $dbh->errorInfo());
	$dbh = null;
	
	saved();
}//TODO: make 1 database instead of 2

function updPL()//update pl
{
	$name = $_GET["name"];
	$newCont = $_GET["newC"];
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->exec("UPDATE playlists SET `pl_contents`='$newCont' WHERE `pl_name`='$name'");
	if ($query == 0)
	  echo "ERROR: Updating playlist entry: \"$name\" to change its contents to: \"$newCont\". Error Info: ".implode(" ", $dbh->errorInfo());
	$dbh = null;
	echo "Saved";
}
?>
