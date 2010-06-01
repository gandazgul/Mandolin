<?php
//check parameters
if (isset($_GET["k"]) and ($_GET["k"] != ""))
	$key = $_GET["k"];
else 
	exit("Malformed URL.");

if (isset($_GET["s"]) and ($_GET["s"] != ""))
	$song_id = $_GET["s"];
else 
	exit("You must provide a valid song ID.");
	
//check the key is valid and current
require_once "../models/users.php";
$mUsers = new UsersModel();
require_once "../models/settings.php";
$settings = new Settings();
$userAuthInfo = json_decode($mUsers->getAuthInfo_json("", $key), true);
$bitrate = json_decode($mUsers->loadSettings("", array('bitrate'), $key), true);
$mUsers->__destruct();
unset($mUsers);

if ($userAuthInfo['isError'])
{
	echo "The key provided is old or invalid<br/><br/>\n";
	exit($userAuthInfo['resultStr']);
}
else
{
	if ((time() - $userAuthInfo['resultStr']['last_key_date']) > $settings->get("keyLastsFor")) die("The key provided is old. This song url is not valid anymore. Login to to Mandolin and get a new one.");
}

if ($bitrate['isError'])
{
	echo "ERROR: Retrieving the user settings. <br />";
	exit($bitrate['resultStr']);
}
else
{
	$bitrate = $bitrate['resultStr']['bitrate'];
}

//get the song name, path and extension
require_once "../models/songs.php";
$result = $mSongs->getSongs("song_path, song_name, song_ext", "song_id", array($song_id));
if ($result->isError)
	exit($result->errorStr);
else
{
	//print_r($result->data);
	$song_path = $result->data[0]['song_path'];
	$song_name = $result->data[0]['song_name'];
	$ext = $result->data[0]['song_ext'];
}
unset($mSongs);

$lameCMD = $settings->get("lameCMD");
$readfile = ($lameCMD == "");

//echoing the file
if (!$readfile)
{
	switch ($ext)
	{
		case "mp3" : {
			$cmd = "$lameCMD --silent --nores --mp3input -m j -b $bitrate -h \"$song_path\" -";
			break;
		}
		case "ogg" : {
			$oggCMD = $settings->get('oggCMD');
			if ($oggCMD == "") 
				$readfile = true;
			else
				$cmd = "$oggCMD \"$song_path\" -o - | $lameCMD --silent --nores -h -b $bitrate - -";
			break;
		}
		case "flac" : {
			$flacCMD = $settings->get('flacCMD');
			if ($flacCMD == "") 
				$readfile = true;
			else
				$cmd = "$flacCMD -dcs \"$song_path\" | $lameCMD --silent --nores -h -b $bitrate - -";
			break;
		}
		default: {	
			$readfile = true;
			break;
		}
	}
}
$settings->__destruct();
unset($settings);

if (($ext == "mp3") or ($ext == "ogg") or ($ext == "flac"))
	header('Content-type: audio/mpeg');
else
	header('Content-type: application/*');
//header("Content-length: ".filesize($song_path) ); //TODO: get the song length
header("Content-Disposition: attachment; filename=\"".$song_name."\"");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Transfer-Encoding: binary");

if ($readfile)
{
	readfile($song_path);
}
else
{
	$blocksize=($bitrate*1024)+1024;
	//echo $bitrate."<br>".$cmd."<br>".$blocksize."<br>";
	$temp = @popen($cmd, "r");
	while ($data = @fread($temp, $blocksize))
		echo $data;
	pclose($temp);
}
?>
