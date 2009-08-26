<?php
//TODO: add try..catch statements on every $query->fetchAll() to output errorInfo().
session_name("newMusicServer");
session_start();
//print_r($_POST);
/*if (!isset($_POST["SID"]) or ($_POST["SID"] != sha1(session_id())))
	header("Location: ./index.php");
*/

$action = $_REQUEST["a"];

try
{
	$action();
}
catch(Exception $e)
{
	echo $e->getMessage();
}

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
		$query = $dbh->query("SELECT song_id, song_name FROM music WHERE `song_album`='$tok' ORDER BY `song_name`");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		for($i = 0; $i < count($queryArr); $i++)
		{
			$sngArr[] = array("id" => $queryArr[$i]["song_id"], "name" => $queryArr[$i]["song_name"]);
		}
		$tok = strtok("|");
	}//from the while
	echo json_encode($sngArr);
	$dbh = null;
}

function mov()
{
	$dbh = new PDO("sqlite:./db/movies.db");
	$movArr = array();	
	$query = $dbh->query("SELECT DISTINCT category FROM movies ORDER BY `category`");
	$catArr = $query->fetchAll();
	//print_r($queryArr);
	$sth = $dbh->prepare("SELECT title, mID FROM movies WHERE category = ?");
	for($i = 0; $i < count($catArr); $i++)
	{
		$cat = $catArr[$i][0];
		$sth->execute(array($cat));
		$movArr = $sth->fetchAll();
		$movies[$i][] = $catArr[$i][0];
		for ($j = 0; $j < count($movArr); $j++)
		{	
			$movies[$i][] = array("id" => $movArr[$j]["mID"], "title" => $movArr[$j]["title"]);
		}
	}
	echo json_encode($movies);
	$dbh = null;
}

function playmov()
{
	$id = $_REQUEST["id"];
	
	$dbh = new PDO("sqlite:./db/movies.db");
	$query = $dbh->query("SELECT title, path FROM movies WHERE mID=$id");
	$queryArr = $query->fetchAll();
	$dbh = null;
	
	$fh = fopen("pl.xml", "wt");
	fwrite($fh, "<?xml version=\"1.0\" encoding=\"utf-8\"?><playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">
		<trackList>
			<track>
				<title>{$queryArr[0]['title']}</title>
				<location>{$queryArr[0]['path']}</location>
			</track>
		</trackList>
	</playlist>");
	fclose($fh);
	
	echo "<embed src='./jwPlayer.swf' width='512' height='404' type='application/x-shockwave-flash' 
			pluginspage='http://www.macromedia.com/go/getflashplayer' 
			bgcolor='#FFFFFF' 
			name='theMediaPlayer' 
			allowfullscreen='true' 
			flashvars='file=pl.xml'>
		  </embed>";
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
	$queries[] = "SELECT art_id, art_name FROM artists WHERE `art_name`  LIKE '%$queryStr%'";
	$queries[] = "SELECT alb_id, alb_name FROM albums WHERE `alb_name` LIKE '%$queryStr%'";
	$queries[] = "SELECT song_id, song_name, song_comments FROM music WHERE `song_name` LIKE '%$queryStr%'";
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

function gett()//returns total artists, albums and songs
{
	$dbh = new PDO("sqlite:./db/music.db");
	$queries = array();
	$queries[] = "SELECT COUNT(art_id) FROM artists";
	$queries[] = "SELECT COUNT(alb_id) FROM albums";
	$queries[] = "SELECT COUNT(song_id) FROM music";
	
	$resultArr = array();
	
	for ($i = 0; $i < 3; $i++)
	{
		$query = $dbh->query($queries[$i]);
		$queryArr = $query->fetchAll();
		$resultArr[] = $queryArr[0][0];		
	}
	
	echo json_encode($resultArr);
	
	$dbh = null;
}

function saved()//returns the list of playlists
{
	$userName = $_SESSION["username"];
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
	$userName = $_SESSION["username"];
	$pl = $_REQUEST["pl"];
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

function play()//makes a list of the tracks selected in the sng list
{
	$name = isset($_REQUEST["pl"]) ? $_REQUEST["pl"] : "playlist";
	
	$fset = fopen("./settings", "rt");
		$musicURL = fgets($fset);
	fclose($fset);
	$musicURL = substr($musicURL, strpos($musicURL, "=") + 1, -1);	
	if (substr($musicURL, -1) != "/")
		$musicURL .= "/";

	header("Content-type: audio/x-mpegurl");//this mime is understood by blackberry
	header("Content-Disposition: filename=\"$name.m3u\"");
	header("Content-Transfer-Encoding: plain");
	echo "#EXTM3U\n";

	if (isset($_REQUEST["sng"]))
	{
		$sng = $_REQUEST["sng"];
	}
	else
	{
		$dbh = new PDO("sqlite:./db/users.db");
		$query = $dbh->query("SELECT pl_contents FROM playlists WHERE `pl_user_name`='".$_SESSION['username']."' AND `pl_name`='$name'");
		$queryArr = $query->fetchAll();
		$dbh = null;
		$sng = $queryArr[0][0];
		//echo $sng;
	}
	$listContents = "[".str_replace("|", ",", substr($sng, 0, -1))."]";	
	
	//echo $listContents;
	$arr = json_decode($listContents);
	if (isset($_REQUEST["rnd"]) && ($_REQUEST["rnd"] == "true")) 
		shuffle($arr);
	//print_r($arr);
	
	$dbh = new PDO("sqlite:./db/music.db");
	$query = $dbh->prepare("SELECT song_id, song_name FROM music WHERE `song_id`=?");
	for ($i = 0; $i < count($arr); $i++)
	{
		$query->execute(array($arr[$i]));
		$queryArr = $query->fetchAll();
		$song_name = $queryArr[0][1];
		$song_id = $queryArr[0][0];
		$ext = substr($song_name, strrpos($song_name, "."));
		$name = substr($song_name, 0, strrpos($song_name, "."));
		echo "#EXTINF:0,$name\n";
		if (isset($_REQUEST['for']) and ($_REQUEST['for'] == 'bb'))
			echo $musicURL."stream.php?k=".$_SESSION["key"]."&b=96&s=$song_id&$ext\n";
		else
			echo $musicURL."stream.php?k=".$_SESSION["key"]."&s=$song_id&$ext\n";		
	}
	$dbh = null;
}

function cpl()//creates a playlist
{
	$userName = $_SESSION["username"];
	$name = $_REQUEST["pl"];
	$sng = $_REQUEST["sng"];
	//echo "$name, $sng, $userName";
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->query("SELECT * FROM playlists WHERE pl_name='$name'");
	if (count($query->fetchAll()) != 0)
	{
		echo "ERROR: Playlist \"$name\" already exists. Please enter a different name.";
		return;
	}
	$query = $dbh->exec("INSERT INTO playlists(pl_name, pl_contents, pl_user_name) VALUES ('$name', '$sng', '$userName')");
	if ($query == 0)
	{		
		$errorArr = $dbh->errorInfo();
		echo "ERROR: Creating the playlist \"$name\": ".$errorArr[2];
		return;
	}
	$dbh = null;

	echo "Playlist: \"$name\" was created successfuly, switch to the \"My Playlists\" tab to play or edit it.";
}

function adds()
{
	$name = $_REQUEST["name"];
	$plCont = $_REQUEST["pl"];
	
	$dbh = new PDO("sqlite:./db/users.db");
		$query = $dbh->query("SELECT pl_contents FROM playlists WHERE `pl_user_name`='".$_SESSION['username']."' AND `pl_name`='$name'");
		$queryArr = $query->fetchAll();
	$dbh = null;
	
	$plCont = $queryArr[0][0].$plCont;
	//echo $plCont;
	
	updPL($name, $plCont);
}

function updPL($name = "", $newCont = "")//update pl
{
	$name = ($name == "") ? $_REQUEST["name"] : $name;
	$newCont = ($newCont == "") ? $_REQUEST["newC"] : $newCont;
	
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->exec("UPDATE playlists SET `pl_contents`='$newCont' WHERE `pl_name`='$name'");
	if ($query == 0)
		echo "ERROR: Updating \"$name\": ".implode(" ", $dbh->errorInfo());
	$dbh = null;
	echo "List content updated successfully";	
}

function del()//deletes a playlist
{
	$userName = $_SESSION["username"];
	$name = $_REQUEST["pl"];
	//echo $name;
	
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->exec("DELETE FROM playlists WHERE `pl_name`='$name' AND `pl_user_name`='$userName'");
	if ($query == 0)
	{
		$errorArr = $dbh->errorInfo();
		echo "ERROR: Deleting playlist \"$name\": ".$errorArr[2];
	}
	$dbh = null;
	
	saved();
}

function shuf()
{
	$name = $_REQUEST['pl'];
	
	$dbh = new PDO("sqlite:./db/users.db");
		$query = $dbh->query("SELECT pl_contents FROM playlists WHERE `pl_user_name`='{$_SESSION['username']}' AND `pl_name`='$name'");
		$queryArr = $query->fetchAll();
	$dbh = null;
	
	$listContents = "[".str_replace("|", ",", substr($queryArr[0][0], 0, -1))."]";
	$arr = json_decode($listContents);
	shuffle($arr);
	$listContents = str_replace(",", "|", substr(json_encode($arr), 1, -1));
	
	updPL($name, $listContents.'|');
	ob_clean();
	retrPL();
}

function ren()//rename a saved playlist
{
	$new_name = $_REQUEST["npl"];
	$name = $_REQUEST["pl"];
	$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->exec("UPDATE playlists SET `pl_name`='$new_name' WHERE `pl_name`='$name'");
	if ($query == 0)
	  echo "ERROR: Renaming playlist \"$name\" to \"$new_name\": ".implode(" ", $dbh->errorInfo());
	$dbh = null;
	
	saved();
}//TODO: make 1 database instead of 2

?>