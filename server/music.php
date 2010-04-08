<?php
session_name("Mandolin");
session_start();
//print_r($_REQUEST);
if (!isset($_REQUEST["SID"]) or ($_REQUEST["SID"] != sha1(session_id())))
{
	header("Location: ..");
	exit();	
}

require_once '../models/artists.php';
require_once '../models/albums.php';
require_once '../models/songs.php';
require_once '../models/playlists.php';

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

unset($musicDB);
unset($usersDB);
unset($moviesDB);

function gett()//returns total artists, albums and songs
{
	global $musicDB;
	
	echo $musicDB->getTotals_json();
}

function artists()
{
	if (isset($_GET["id"]))
	{
		//output infor about the album wich id is: $_GET["id"]
	}
	else//list all artists
	{
		$artists = new ArtistsModel(null);
		$artResult = $artists->getArtists();
		if ($artResult->isError)
		{
			echo json_encode($artResult);
		}
		else
		{
			echo json_encode($artResult->data);
		}
		$artists->__destruct();
		unset($artists);
	}
}

function albums()
{
	if (isset($_GET["id"]))
	{
		//output infor about the album wich id is: $_GET["id"]
	}
	else
	if (isset($_REQUEST["artist_id"]))
	{
		$albums = new AlbumsModel($_REQUEST["artist_id"]);
		$albResult = $albums->getAlbums();
		if ($albResult->isError)
		{
			echo json_encode($albResult);
		}
		else
		{
			echo json_encode($albResult->data);
		}
		$albums->__destruct();
		unset($albums);
	}
	else
	{
		//list all albums
	}	
}

function songs()
{
	$songs = new SongsModel(null, null, null);

	if (isset($_GET['id']))
	{
		//echo information about the song and link for lyrics
		$songs->song_id = $_GET['id'];
	}
	else 
	if (isset($_GET['album_id'])) { $songs->song_album = $_GET['album_id']; }
	else
	if (isset($_GET['art_id'])) { $songs->song_art = $_GET['art_id']; }

	$sngResult = $songs->getSongs();
	if ($sngResult->isError) { echo json_encode($sngResult); }
	else
	{ echo json_encode($sngResult->data); }
	
	unset ($songs);
}

function search()
{
	global $musicDB;
	
	echo $musicDB->search_json($_GET["q"]);
}

function play()//makes a list of the tracks selected in the sng list
{
	global $usersDB, $settings, $playlists;

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

		header("Content-type: ".$playlists->plFormatsMimeTypes[$plFormat]);
		header("Content-Disposition: filename=\"$name.$plFormat\"");
		header("Content-Transfer-Encoding: plain");
		echo $playlists->get_file($plFormat, $plArr, $musicURL);
	}
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

?>