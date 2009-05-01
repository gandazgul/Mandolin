<?php
/*/TODO: add try..catch statements on every $query->fetchAll() to output errorInfo().
session_name("newMusicServer");
session_start();
if ($_POST["SID"] != sha1(session_id()))
	die("session invalid");
*/
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
	$tok = strtok($artist, "|");
	while($tok !== false)
	{
		$query = $dbh->query("SELECT alb_id, alb_name FROM albums WHERE `alb_art_id`='$tok' ORDER BY `alb_name`");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		$albArr = array();
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
	$tok = strtok($alb, "|");
	while($tok !== false)
	{
		$query = $dbh->query("SELECT song_id, song_name, song_comments FROM music WHERE `song_album`='$tok' ORDER BY `song_name`");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		$sngArr = array();
		for($i = 0; $i < count($queryArr); $i++)
		{
			$sngArr[] = array("id" => $queryArr[$i]["song_id"], "name" => $queryArr[$i]["song_name"], "comm" => $queryArr[$i]["song_comments"]);
		}
		$tok = strtok("|");
	}//from the while
	echo json_encode($sngArr);
	$dbh = null;
}

function saved()//returns the list of playlists
{
	global $userName;
	
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->query("SELECT pl_name FROM playlists WHERE `pl_user_name`='$userName'");
	$queryArr = $query->fetchAll();
	for($i = 0; $i < count($queryArr); $i++)
	{
		echo "<option>".$queryArr[$i][0]."</option>\n";
	}
	$dbh = null;
}

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

function add_com()//add a comment to a track
{
	$sng = $_GET["sng"];
	$com = $_GET["com"];
	$dbh = new PDO("sqlite:./db/music.db");
	$query = $dbh->exec("UPDATE music SET `song_comments`='$com' WHERE `song_id`='$sng'");
	if ($query == 0)
	  echo "ERROR: Updating song entry: $sng to add comments: $com".implode(" ", $dbh->errorInfo());
	$dbh = null;
	
	sng();
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
}

function retrPL() //this function retreives the contents of the specified playlist
{
	global $userName, $action;
	
	$name = $_GET["name"];
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->query("SELECT pl_contents FROM playlists WHERE `pl_user_name`='$userName' AND `pl_name`='$name'");
	$queryArr = $query->fetchAll();
	$dbh = null;
	$listArr = array();
	$tok = strtok($queryArr[0][0], "|");
	$dbh = new PDO("sqlite:./db/music.db");
	while($tok !== false)
	{
		$query = $dbh->query("SELECT song_name FROM music WHERE `song_id`='$tok'");
		$queryArr = $query->fetchAll();
		echo "<option value=\"$tok\">".$queryArr[0][0]."</option>";
		$tok = strtok("|");
	}
	$dbh = null;
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
