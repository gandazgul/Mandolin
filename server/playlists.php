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

function playlists()//returns the list of playlists/the contents of playlist->ID/Create a new playlist
{
	global $playlists, $songs;

	if (isset($_POST['pl_name']))
	{
		$pl_name = $_POST["pl_name"];
		if ($playlists->post($pl_name, $_POST["pl_contents"]))
			echo "Playlist: \"$pl_name\" was created successfuly, switch to the \"Music Playlists\" tab to play or edit it.";
	}
	else if (isset($_GET['id']))
	{
		$plContents = $playlists->get($_GET['id']);
		//print_r($plContents);
		echo $songs->getInfo_json($plContents, array('song_id', 'song_name'));
	}
	else
		echo $playlists->get_json(null);
}

function delete()//deletes a playlist
{
	global $playlists;
	
	if ($playlists->delete($_GET["id"]))
		echo $playlists->get_json(null);
}

function put()//update playlist
{
	global $playlists;

	$id = $_GET['id'];
	$data = json_decode($_GET["data"], true);

	if (isset($_GET['concat']))
	{
		$data['pl_contents'] = "pl_contents || ".$data['pl_contents'];
	}

	if($playlists->put($id, $data))
	{
		if (isset($data['pl_contents']))
			echo $playlists->get_json($id);
		else
			echo $playlists->get_json(null);
	}
}

//TODO: do the shuffle with javascript and use PUT
function shuf()//shuffles a playlist
{
	global $playlists;
	
	$plID = $_GET["id"];
	
	$plContents = $playlists->get($plID);
	shuffle($plContents);
	$data["pl_contents"] = "'".implode("|", $plContents)."|'";

	if($playlists->put($plID, $data))
	{
		playlists();
	}
}

?>