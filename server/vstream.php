<?php
/*$cur_key = $_GET["k"]; 
if ($cur_key == "") exit("The key is invalid");
$video_id = $_GET["id"];
if ($video_id == "")	die("You must provide a valid song ID.");

$dbh = new PDO("sqlite:./db/users.db");
	$query = $dbh->query("SELECT last_key_date FROM users WHERE `last_key`='$cur_key'");
	$queryArr = $query->fetchAll();
$dbh = null;
if (count($queryArr) == 0)
	die('The key is invalid');
$last_key_date = $queryArr[0][0];
if ((time() - $last_key_date) > 64800) die("The key is old");

$dbh = new PDO("sqlite:./db/videos.db");
	$query = $dbh->query("SELECT video_path, video_name FROM videos WHERE `video_id`='$video_id'");
	$queryArr = $query->fetchAll();
$dbh = null;
if (count($queryArr) == 0)
	die('Song ID not found in Database.');
//print_r($queryArr);
$video_path = $queryArr[0][0];
$video_name = $queryArr[0][1];*/
$video_path = "./a.flv";
$video_name = "a.flv";
$ext = substr($video_name, strrpos($video_name, ".") + 1);
//echo $ext;

header("Content-type: video/x-flv");
header("Content-length: ".filesize($video_path) );
header("Content-Disposition: filename=\"".$video_name."\"");
header("Content-Transfer-Encoding: binary");

if ($ext != 'flv')
{
	//encode with ffmpeg then play.
}
else
{
	readfile($video_path);	
}
?>