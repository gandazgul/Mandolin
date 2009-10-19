<?php
$cur_key = $_GET["k"]; 
if ($cur_key == "") exit("The key is invalid");
$song_id = $_GET["s"];
if ($song_id == "")	die("You must provide a valid song ID.");

$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->query("SELECT last_key_date FROM users WHERE `last_key`='$cur_key'");
	$queryArr = $query->fetchAll();
$dbh = null;
if (count($queryArr) == 0)
	die('The key is invalid');
$last_key_date = $queryArr[0][0];
if ((time() - $last_key_date) > 64800) die("The key is old");

$dbh = new PDO("sqlite:./db/music.db");
	$query = $dbh->query("SELECT song_path, song_name FROM music WHERE `song_id`='$song_id'");
	$queryArr = $query->fetchAll();
$dbh = null;
if (count($queryArr) == 0)
	die('Song ID not found in Database.');
//print_r($queryArr);
$song_path = $queryArr[0][0];
$song_name = $queryArr[0][1];
	
$ext = substr($song_name, strrpos($song_name, ".") + 1);
//echo $ext;
switch ($ext)
{
  case "mp3" : 
  {
	header('Content-type: audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3, audio/mpeg, audio/x-mp3');
  	break;
  }
  case "ogg" : 
  {
	header('Content-type: application/ogg');
  	break;
  }
  case "flac" : 
  {
	header('Content-type: audio/flac');
	break;
  }
}
header("Content-length: ".filesize($song_path) );
header("Content-Disposition: filename=\"".$song_name."\"");
header("Content-Transfer-Encoding: binary");

if (isset($_GET['b']))
{
	$bitrate = $_GET['b'];
	$cmd = "/usr/bin/lame --silent --nores --mp3input -m j -b $bitrate -h \"$song_path\" -";
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