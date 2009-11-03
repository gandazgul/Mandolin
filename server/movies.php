<?php
//TODO: add try..catch statements on every $query->fetchAll() to output errorInfo().
//TODO: covert all this into the DB Class.
session_name("newMusicServer");
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