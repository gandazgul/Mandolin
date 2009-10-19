<?php
//TODO: add try..catch statements on every $query->fetchAll() to output errorInfo().
//TODO: covert all this into the DB Class.
session_name("newMusicServer");
session_start();
//print_r($_POST);
/*if (!isset($_POST["SID"]) or ($_POST["SID"] != sha1(session_id())))
	header("Location: ./index.php");
*/

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

//--------------------------------------------------------------Functions for Music---------------------------------------
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

//--------------------------------------------------------------Functions for Music and Playlists---------------------------------------
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

//--------------------------------------------------------------Functions for Playlists---------------------------------------
function saved()//returns the list of playlists
{
	global $usersDB;
	
	echo $usersDB->getPLsForUser_json($_SESSION["username"]);
}

function retrPL() //this function retreives the contents of the specified playlist(s)
{
	global $musicDB, $usersDB;
	
	$pl = $_REQUEST["pl"];
	
	$plContents = $usersDB->getPLContents($_SESSION["username"], $pl);
	//print_r($plContents);	
	echo $musicDB->getPLContents_json($plContents);
}

function cpl()//creates a playlist
{
	global $usersDB;
	
	$userName = $_SESSION["username"];
	$plName = $_REQUEST["pl"];
	$plContent = $_REQUEST["content"];
	
	if ($usersDB->createPlaylist($userName, $plName, $plContent))
		echo "Playlist: \"$plName\" was created successfuly, switch to the \"Music Playlists\" tab to play or edit it."; 
}

function updPL()//update pl
{
	global $usersDB;
	
	$plName = $_REQUEST["name"];
	$newContent = $_REQUEST["newC"];
	$concat = $_REQUEST["concat"];
	
	if($usersDB->updatePL($plName, $newContent, $concat))
		echo "List content updated successfully";
}

function del()//deletes a playlist
{
	global $usersDB;
	
	$userName = $_SESSION["username"];
	$plName = str_replace("'", "''", $_REQUEST["pl"]);
	//echo $plName;
	
	if ($usersDB->deletePL($userName, $plName))
		echo $usersDB->getPLsForUser_json($_SESSION["username"]);
}

function shuf()//shuffles a playlist
{
	global $usersDB, $musicDB;
	
	$plName = $_REQUEST['pl'];
	
	$plContents = $usersDB->getPLContents($_SESSION["username"], $plName);
	//print_r($plContents);
	shuffle($plContents);
	$newContent = implode("|", $plContents)."|";
	//echo $newContent;
	
	if($usersDB->updatePL($plName, $newContent, "false"))
		echo $musicDB->getPLContents_json($plContents);
}

function ren()//rename a saved playlist
{
	global $usersDB;
	
	$newName = str_replace("'", "''", $_REQUEST["npl"]);
	$name = str_replace("'", "''", $_REQUEST["pl"]);
	
	if ($usersDB->renamePL($name, $newName))
		echo $usersDB->getPLsForUser_json($_SESSION["username"]);
}

//--------------------------------------------------------------Functions for Movies---------------------------------------
function mov()//list all movies by category
{
	global $moviesDB;
	
	echo $moviesDB->getMovies_json();
}

function playmov()//play selected movie
{
	global $moviesDB, $settings;
	
	echo $moviesDB->getMovieEmbedCode($_REQUEST["id"], $settings->get('baseURL'));
}

?>