<?php
//TODO: add try..catch statements on every $query->fetchAll() to output errorInfo().
//TODO: covert all this into the DB Class.
session_name("Mandolin");
session_start();
//print_r($_POST);
if (!isset($_POST["SID"]) or ($_POST["SID"] != sha1(session_id())))
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

function art()
{
	global $musicDB;
	
	echo $musicDB->getArtists_json();
}

function alb()
{
	global $musicDB;

	echo $musicDB->getAlbums_json($_REQUEST["artist"]);
}

function sng()
{
	global $musicDB;
	
	echo $musicDB->getSongs_json($_REQUEST["alb"]);
}

function search()
{
	global $musicDB;
	
	$queryStr = $_REQUEST["q"];
	
	echo $musicDB->search_json($queryStr); 
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
	
	$forBB = (isset($_REQUEST['for']) and ($_REQUEST['for'] == 'bb'));
	
	header("Content-type: application/xspf+xml");
	header("Content-Disposition: filename=\"$name.xspf\"");
	header("Content-Transfer-Encoding: plain");
	echo $musicDB->getXSPFPlaylist($plArr, $forBB, $musicURL);
}

?>