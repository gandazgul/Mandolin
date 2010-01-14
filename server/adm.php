<?php
session_name("Mandolin");
session_start();
//print_r($_POST);
if (!isset($_POST["SID"]) or ($_POST["SID"] != sha1(session_id())))
{
	header("Location: ..");
	exit();	
}

require_once("../models/UsersDB.php");
$usersDB = new UsersDB();
require_once("../models/MusicDB.php");
$musicDB = new MusicDB();

$action = $_POST["a"];

try
{
	$action();
}
catch(Exception $e)
{
	echo $e->getMessage();
}

$usersDB->__destruct();
unset($usersDB);
$settings->__destruct();
unset($settings);
$musicDB->__destruct();
unset($musicDB);


function addFolderToDB()
{
	global $musicDB;
	
	$resultArr = array();
	$resultArr["isError"] = false;
	if (is_dir($_POST["f"]))
	{
		if ($musicDB->addToDB($_POST["f"], strlen($_POST["f"])))
		{
			$resultArr["resultStr"] = $_POST["f"];
		}
		else
		{
			$resultArr["isError"] = true;
			$resultArr["resultStr"] = "There was an error adding the specified folder to the DB.";
		}
	}
	else
	{
		$resultArr["isError"] = true;
		$resultArr["resultStr"] = "The specified folder doesn't exist.";
	}
	
	echo json_encode($resultArr);
}

function set()
{
	global $settings;
	
	//echo $_POST['data'];
	$data = json_decode(stripslashes($_POST['data']), true);
	//print_r($data);
	for ($i = 0; $i < count($data['keys']); $i++)
	{
		$settings->set($data['keys'][$i], $data['values'][$i]);
	}
	
	echo "Settings saved successfully";
}

function get()
{
	global $settings;
	
	$keys = json_decode(stripslashes($_POST['keys']), true);
	$result = array();
	
	for ($i = 0; $i < count($keys); $i++)
	{	
		$result[$keys[$i]] = $settings->get($keys[$i]);
	}
	
	echo json_encode($result);
}

function uset()
{
	global $usersDB;
	
	//echo $_POST['data'];
	$resultArr = json_decode($usersDB->saveSettings($_SESSION['username'], stripslashes($_POST['data'])), true);
	echo $resultArr['resultStr'];
}

function uget()
{
	global $usersDB;
	
	echo $usersDB->loadSettings($_SESSION['username'], json_decode(stripslashes($_POST['keys']), true)); 
}

function cpassw()
{
	global $usersDB;
	
	$user = $_SESSION["username"];
	
	if ($usersDB->verifyPassw($user, $_POST["op"]))
	{
		//echo $_SESSION["userAdminLevel"];
		echo $usersDB->alterUser(0, $user, $_SESSION["userAdminLevel"], $_POST["np"]);
	}
	else
		echo "ERROR: The password you entered is wrong.";
}

function saveu()
{
	global $usersDB;

	echo $usersDB->alterUser($_POST['id'], $_POST['un'], $_POST['adm'], $_POST['p']);
}

function addu()
{
	global $usersDB;
	
	echo $usersDB->addNewUser($_POST['u'], $_POST['p'], $_POST['adm']);
}

function delU()
{
	global $usersDB;
	
	echo $usersDB->deleteUser($_POST['id']);
}

?>