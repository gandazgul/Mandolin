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

require_once '../models/music.php';
$mMusic = new MusicModel();
require_once '../models/MoviesDB.php';
$moviesDB = new MoviesDB();
require_once '../models/users.php';
$mUsers = new UsersModel();

$action = $_REQUEST["a"];

try
{
	$action();
}
catch(Exception $e)
{
	echo $e->getMessage();
}

unset($playlists);
unset($mSongs);
unset($mMusic);
unset($mUsers);
unset($moviesDB);

function gett()//returns total artists, albums and songs
{
	global $mMusic;
	
	echo $mMusic->getTotals_json();
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
		echo json_encode($artists->getArtists());
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
		echo json_encode($albums->getAlbums());
		unset($albums);
	}
	else
	{
		//list all albums
	}	
}

function songs()
{
	global $mSongs;
	$whereCol = $whereVal = null;

	if (isset($_GET['id']))		//echo information about the song and link for lyrics
	{
		$whereCol = 'song_id';
		$whereVal = explode("|", $_GET['id'], -1);
	}
	else 
	if (isset($_GET['album_id'])) 
	{
		$whereCol = "song_album";
		$whereVal = explode("|", $_GET['album_id']);
	}
	else
	if (isset($_GET['art_id']))
	{
		$whereCol = "song_art";
		$whereVal = explode("|", $_GET['art_id'], -1);
	}

	echo json_encode($mSongs->getSongs("song_id, song_name", $whereCol, $whereVal));
}

function allsongs()
{
	global $mSongs;

	echo json_encode($mSongs->getAllSongs());
}

function search()
{
	global $mMusic;
	
	echo json_encode($mMusic->search($_GET["q"]));
}

function play()//makes a list of the tracks selected in the sng list
{
	global $settings, $playlists, $mSongs, $mUsers;

	$plName = "playlist";
	$plArr = array();
	$musicURL = $settings->get('baseURL');
	if (substr($musicURL, -1) != "/")
		$musicURL .= "/";

	if (isset($_REQUEST["sng"]))
	{
		$sng = $_REQUEST["sng"];
		$plArr = explode('|', $sng);
	}
	else
	if (isset ($_REQUEST["pl_id"]))
	{
		$plArr = $playlists->get($_REQUEST["pl_id"]);
	}
	else
	{
		if (isset ($_REQUEST['art_id']))
		{
			$whereCol = "song_art";
			$whereVal = explode("|", $_REQUEST['art_id']);
		}
		else
		if (isset ($_REQUEST['alb_id']))
		{
			$whereCol = "song_album";
			$whereVal = explode("|", $_REQUEST['alb_id']);
		}
	}
	if (isset($whereCol))//if either art_id or alb_id was defined
	{
		$result = $mSongs->getSongs("song_id", $whereCol, $whereVal);
		if ($result->isError)
			die("ERROR: " . $result->errorStr);
		else
		{
			foreach ($result->data as $row)
			{
				$plArr[] = $row['song_id'];
			}
		}
	}

	if (count($plArr) == 0) die("Nothing to play");

	//print_r($plArr); //print what we have so far
	if (isset($_REQUEST["rnd"]) and ($_REQUEST["rnd"] == "true"))
		shuffle($plArr);
	//print_r($plArr); //check if it was randomized

	$plFormat = json_decode($mUsers->loadSettings($_SESSION['username'], array("plFormat")), true);
	if ($plFormat['isError'])
	{
		echo $plFormat['resultStr'];
	}
	else
	{
		$plFormat = $plFormat['resultStr']['plFormat'];

		header("Content-type: ".$playlists->plFormatsMimeTypes[$plFormat]);
		header("Content-Disposition: attachment; filename=\"$plName.$plFormat\"");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
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