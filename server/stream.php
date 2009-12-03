<?php
//check parameters
if (isset($_GET["k"]) and ($key != ""))
	$key = $_GET["k"];
else 
	exit("Malformed URL.");

if (isset($_GET["s"]) and ($key != ""))
	$song_id = $_GET["s"];
else 
	exit("You must provide a valid song ID.");
	
//check the key is valid and current
require_once "../models/UsersDB.php";
$usersDB = new UsersDB();
$userAuthInfo = json_decode($usersDB->getAuthInfo_json("", $key), true);
$usersDB->__destruct();
unset($usersDB);

if ($userAuthInfo['isError'])
{
	echo "The key provided is old or invalid<br/><br/>\n";
	exit($userAuthInfo['resultStr']);
}
else
{
	require_once "../models/Settings.php";
	$settings = new Settings();
	if ((time() - $userAuthInfo['resultStr']['last_key_date']) > $settings->get("keyLastsFor")) die("The key provided is old. This song url is not valid anymore. Login to to Mandolin and get a new one.");
	$settings->__destruct();
	unset($settings);
}

//get the song name, path and extension
require_once "../models/MusicDB.php";
$musicDB = new MusicDB();
$resultArr = $musicDB->getColumnsFromID($song_id, array("song_path", "song_name", "song_ext"));
if ($resultArr['isError'])
	exit($resultArr['resultStr']);
else
{
	//print_r($resultArr['resultStr']);
	$song_path = $resultArr['resultStr'][0]['song_path'];
	$song_name = $resultArr['resultStr'][0]['song_name'];
	$ext = $resultArr['resultStr'][0]['song_ext'];
}
$musicDB->__destruct();
unset($musicDB);

//echoing the file
switch ($ext)
{
	case "mp3" : {
		header('Content-type: audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3, audio/mpeg, audio/x-mp3');
		break;
	}
	case "ogg" : {
		header('Content-type: application/ogg');
		break;
	}
	case "flac" : {
		header('Content-type: audio/flac');
		break;
	}
	default: {
		header('Content-type: audio/x');
		break;
	}
}
header("Content-length: ".filesize($song_path) );
header("Content-Disposition: filename=\"".$song_name."\"");
header("Content-Transfer-Encoding: binary");

if (isset($_GET['b']))
{
	$bitrate = $_GET['b'];
	$cmd = "/usr/bin/lame --silent --nores -m j -b $bitrate -h \"$song_path\" -";
	$blocksize=($bitrate*1024)+1024;
	//echo $bitrate."<br>".$cmd."<br>".$blocksize."<br>";
	$temp = @popen($cmd, "r");
	while ($data = @fread($temp, $blocksize))
		echo $data;
	pclose($temp);
}
else
{
	readfile($song_path);	
}
?>
