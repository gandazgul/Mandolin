<?php
session_name("Mandolin");
session_start();
//print_r($_POST);
if (!isset($_REQUEST["SID"]) or ($_REQUEST["SID"] != sha1(session_id())))
{
	header("Location: ..");
	exit();	
}

require_once '../models/playlists.php';
require_once '../models/songs.php';

require_once '../models/MusicDB.php';
$musicDB = new MusicDB();
require_once '../models/MoviesDB.php';
$moviesDB = new MoviesDB();
require_once '../models/UsersDB.php';
$usersDB = new UsersDB();

$action = $_REQUEST["a"];

try
{
	$action();
}
catch(Exception $e)
{
	echo $e->getMessage();
}

$playlists->__destruct();
unset($playlists);
$songs->__destruct();
unset($songs);

unset($musicDB);
unset($usersDB);
unset($moviesDB);

function playlists()//returns the list of playlists
{
	global $playlists, $songs;

	if (isset($_REQUEST['id']))
	{
		$plContents = $playlists->get($_REQUEST['id']);
		//print_r($plContents);
		echo $songs->getInfo_json($plContents, array('song_id', 'song_name'));
	}
	else
		echo $playlists->get_json(null);
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

?>