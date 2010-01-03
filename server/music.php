<?php
session_name("Mandolin");
session_start();
//print_r($_POST);
if (!isset($_REQUEST["SID"]) or ($_REQUEST["SID"] != sha1(session_id())))
{
	header("Location: ..");
	exit();	
}

require_once '../models/MusicDB.php';
$musicDB = new MusicDB();
require_once '../models/MoviesDB.php';
$moviesDB = new MoviesDB();
require_once '../models/UsersDB.php';
$usersDB = new UsersDB();
require_once '../models/Settings.php';
$settings = new Settings();

$action = $_REQUEST["a"];

try
{
	$action();
}
catch(Exception $e)
{
	echo $e->getMessage();
}

unset($musicDB);
unset($usersDB);
unset($moviesDB);
unset($settings);

function gett()//returns total artists, albums and songs
{
	global $musicDB;
	
	echo $musicDB->getTotals_json();
}

function artists()
{
	global $musicDB;
	
	if (isset($_POST["id"]))
	{
		//output infor about the artist wich id is: $_POST["id"]
	}
	else
		echo $musicDB->getArtists_json();
}

function albums()
{
	global $musicDB;

	if (isset($_POST["id"]))
	{
		//output infor about the artist wich id is: $_POST["id"]
	}
	else if (isset($_POST["artist_id"]))
	{
		echo $musicDB->getAlbums_json($_POST["artist_id"]);
	}
	else
	{
		//list all albums	
	}	
}

function sng()
{
	global $musicDB;
	
	echo $musicDB->getSongs_json($_POST["alb"]);
}

function search()
{
	global $musicDB;
	
	$queryStr = $_POST["q"];
	
	echo $musicDB->search_json($queryStr); 
}

function addc()//add a comment to a track
{
	$sng = $_POST["sng"];
	$com = $_POST["com"];
	$dbh = new PDO("sqlite:./db/music.db");
	$query = $dbh->exec("UPDATE music SET `song_comments`='$com' WHERE `song_id`='$sng'");
	if ($query == 0)
	  echo "ERROR: Updating song entry: $sng to add comments: $com".implode(" ", $dbh->errorInfo());
	$dbh = null;
	
	sng();
}

function play()//makes a list of the tracks selected in the sng list
{
	global $settings, $musicDB, $usersDB;
	
	$name = isset($_REQUEST["pl"]) ? $_REQUEST["pl"] : "playlist";
	$musicURL = $settings->get('baseURL');
	if (substr($musicURL, -1) != "/")
		$musicURL .= "/";

	if (isset($_REQUEST["sng"]))
	{
		$sng = $_REQUEST["sng"];
		$plArr = explode('|', $sng, -1);
	}
	else
	{
		$plArr = $usersDB->getPLContents($_SESSION["username"], $name);
	}
	//print_r($plArr);
	if (isset($_REQUEST["rnd"]) and ($_REQUEST["rnd"] == "true")) 
		shuffle($plArr);
	//print_r($plArr);
	
	$plFormat = json_decode($usersDB->loadSettings($_SESSION['username'], array("plFormat")), true);
	if ($plFormat['isError'])
	{
		echo $plFormat['resultStr'];
	}
	else
	{
		$plFormat = $plFormat['resultStr']['plFormat'];
		
		header("Content-type: ".$musicDB->plFormatsMimeTypes[$plFormat]);
		header("Content-Disposition: filename=\"$name.$plFormat\"");
		header("Content-Transfer-Encoding: plain");
		echo $musicDB->getPlaylist($plFormat, $plArr, $musicURL);
	}
}

?>