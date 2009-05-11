<?php
//TODO: add try..catch statements on every $query->fetchAll() to output errorInfo().
session_name("newMusicServer");
session_start();
//print_r($_POST);
/*if (!isset($_POST["SID"]) or ($_POST["SID"] != sha1(session_id())))
	header("Location: ./index.php");
*/
$dbh = new PDO("sqlite:./db/users.db");
$action = $_REQUEST["a"];

try
{
	$action();
}
catch(Exception $e)
{
	echo $e->getMessage();
}

function cpassw()
{
	global $dbh;
	$op = $_REQUEST["op"];
	$np = $_REQUEST["np"];
	$user = $_SESSION["username"];
		
    $query = $dbh->query("SELECT user_password FROM users WHERE `user_name`='$user'");
	$queryArr = $query->fetchAll();
	
	if (sha1($op) == $queryArr[0][0])
	{
		$dbh->query("UPDATE users SET `user_password`='".sha1($np)."' WHERE `user_name`='$user'");
		echo "Password successfully changed.";
	}
	else
		echo "ERROR: The password you entered is wrong";
}

function updDB()
{
	echo $art." TODO: implement this";
}

function addNewUser()
{
	global $dbh;
	$user = $_GET["user"];
	$passw = md5($_GET["passw"]);
	$adminLvl = $_GET["adminLvl"];
	
	$dbh->exec("INSERT INTO users(user_name, user_password, user_admin_level) VALUES ('$user', '$passw', $adminLvl)") or
		die("FATAL ERROR: While adding a new user to the database. ".implode(" ", $dbh->errorInfo()));
	echo "User successfully added.";
}
?>