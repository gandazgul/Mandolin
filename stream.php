<?php
$dbh = new PDO("sqlite:./db/users.db");
$cur_key = $_GET["k"]; 
$song_id = $_GET["s"];
$query = $dbh->query("SELECT last_key_date FROM users WHERE `last_key`='$cur_key'");
$queryArr = $query->fetchAll();
$last_key_date = $queryArr[0][0];
//echo $last_key_date;
$dbh = null;

if ($last_key_date == "") die("The key is invalid");
if ((time() - $last_key_date) > 604800) die("The key is old");
if ($song_id == "")	die("No Song ID? I dont read minds :P");

$dbh = new PDO("sqlite:./db/music.db");
$query = $dbh->query("SELECT song_path, song_name FROM music WHERE `song_id`='$song_id'");
$queryArr = $query->fetchAll();
//print_r($queryArr);
$song_path = $queryArr[0][0];
$song_name = $queryArr[0][1];
$ext = substr($song_name, strrpos($song_name, ".") + 1);
//echo $ext;
switch ($ext)
{
  case "mp3" : 
  {
	header('Content-type: audio/mpeg');
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
readfile($song_path);
$dbh = null;
?>
