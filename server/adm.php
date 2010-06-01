<?php
session_name("Mandolin");
session_start();
//print_r($_POST);
if (!isset($_REQUEST["SID"]) or ($_REQUEST["SID"] != sha1(session_id())))
{
	header("Location: ..");
	exit();
}

require_once("../models/users.php");
$mUsers = new UsersModel();
require_once("../models/music.php");
$mMusic = new MusicModel();
require_once '../models/settings.php';

$action = $_REQUEST["a"];

try
{
	$action();
}
catch(Exception $e)
{
	echo $e->getMessage();
}

unset($mUsers);
unset($settings);
unset($mMusic);


function addFolderToDB()
{
	global $mMusic;
	
	$resultArr = array();
	$resultArr["isError"] = false;
	if (is_dir($_POST["f"]))
	{
		if ($mMusic->addToDB($_POST["f"], strlen($_POST["f"])))
		{
			$resultArr["resultStr"] = $_POST["f"];
		}
		else
		{
			$resultArr["isError"] = true;
			$resultArr["resultStr"] = "ERROR: There was an error adding the specified folder to the DB.";
		}
	}
	else
	{
		$resultArr["isError"] = true;
		$resultArr["resultStr"] = "ERROR: The specified folder doesn't exist.";
	}
	
	echo json_encode($resultArr);
}

function set()
{
	global $settings;
	
	//echo $_POST['data'];
	$data = json_decode($_POST['data'], true);
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
	global $mUsers;

	echo json_encode($mUsers->saveSettings($_SESSION['username'], stripslashes($_POST['data'])));
}

function uget()
{
	global $mUsers;
	
	echo $mUsers->loadSettings($_SESSION['username'], json_decode(stripslashes($_POST['keys']), true));
}

function cpassw()
{
	global $mUsers;
	$result = new Result();

	$user = $_SESSION["username"];
	
	if ($mUsers->verifyPassw($user, $_POST["op"]))
	{
		//echo $_SESSION["userAdminLevel"];
		echo json_encode($mUsers->alterUser(0, $user, $_SESSION["userAdminLevel"], $_POST["np"]));
	}
	else
	{
		$result->isError = true;
		$result->errorStr = "ERROR: The password you entered is wrong.";
		echo json_encode($result);
	}
}

function saveu()
{
	global $mUsers;

	//print_r($_POST);
	echo json_encode($mUsers->alterUser($_POST['id'], $_POST['un'], $_POST['adm'], $_POST['p']));
}

function addu()
{
	global $mUsers;

	echo $mUsers->addNewUser($_POST['u'], $_POST['p'], $_POST['adm']);
}

function delU()
{
	global $mUsers;
	
	echo $mUsers->deleteUser($_POST['id']);
}

function post()
{
	global $settings, $result;

	$uploaddir = $settings->get('baseDir');
	if ((substr($uploaddir, -1) != '/') and (substr($uploaddir, -1) != '\\'))
		$uploaddir .= "/";
	$uploaddir .= "data/";
	$uploadfile = $uploaddir . basename($_FILES['usersFile']['name']);
	//$uploadfile = $uploaddir . 'users.csv'; echo $uploadfile;
	
	if (move_uploaded_file($_FILES['usersFile']['tmp_name'], $uploadfile))
	{
		$fh = fopen($uploadfile, 'r');
		if ($fh)
		{
			$columnsArr = fgetcsv($fh);
			//print_r($columnsArr);
			$result->isError = false;
			$result->strResult = array();
			//$fieldsArr = array('user_name', 'user_password', 'user_settings', 'user_admin_level');
			while (!feof($fh))
			{
				$csv = fgetcsv($fh);
				if ($csv != "")
					$result->strResult[] = array_combine($columnsArr, $csv);
			}
			fclose($fh);
		}
		else
		{
			$result->strResult = "ERROR: Couldnt open the uploaded file.";
		}
	}
	else
	{
		$result->strResult = "ERROR: File was not uploaded correctly.";
	}

	echo json_encode($result);
}

?>