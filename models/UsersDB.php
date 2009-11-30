<?php
class UsersDB 
{
	private $dbfilepath;
	private $dbh;
	
	function __construct($dbfilepath = "../models/dbfiles/users.db")
	{
		$this->dbfilepath = $dbfilepath;
		$this->dbh = new PDO("sqlite:$this->dbfilepath");
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	function __destruct()
	{
		$this->dbh = null;
	}
	
	//-------------------------------------------------------------- User functions ----------------------------------------------------------
	function listUsers()
	{
		$query = $this->dbh->query("SELECT user_name, user_admin_level, user_id FROM users");
		return $query->fetchAll();
	}
	
	function listUsers_json()
	{
		return json_encode($this->listUsers());
	}
	
	function alterUser($id, $username, $adm, $passw)
	{
		$queryStr = "UPDATE users SET ";

		if ($adm == 'true')
			$queryStr .= "user_admin_level='1'";
		else
			$queryStr .= "user_admin_level='0'";
		if ($passw != "")
			$queryStr .= ", user_password='".sha1($passw)."'";
		//echo $queryStr;
		
		if($username != "")
			$whereClause = " WHERE user_name='$username'";
		else
			$whereClause = " WHERE user_id=$id";
		
		try
		{
			$this->dbh->exec($queryStr.$whereClause);
		}
		catch(PDOException $e)
		{
			return "ERROR: Saving user information: ".$e->getMessage();
		}
		
		return "User information saved successfully";
	}
	
	function addNewUser($user_name, $user_passw, $user_adm_level)
	{
		$resultArr = array();
		$resultArr["isError"] = false;
		$user_adm_level = ($user_adm_level == "true") ? 1 : 0;
		
		$result = $this->dbh->exec("INSERT INTO users(user_name, user_password, user_admin_level) VALUES ('$user_name', '".sha1($user_passw)."', $user_adm_level)");
		if ($result == 0)
		{
			$error = $this->dbh->errorInfo();
			$resultArr["isError"] = true;
			$resultArr["resultStr"] = "FATAL ERROR: While creating a new user: ".$error[2];
		}
		else
		{
			$result = $this->dbh->query("SELECT max(user_id) FROM users");
			$result = $result->fetchAll();
			$id = $result[0][0];
			
			$resultArr["resultStr"] = "<tr id='tr$id'>";
			$resultArr["resultStr"] .= "<td><input type='checkbox' name='userCheck$id' id='userCheck$id' value='$id' />";
			$resultArr["resultStr"] .= "<label for='userCheck$id' style='display: inline; '>&nbsp;&nbsp;";
			$resultArr["resultStr"] .= $user_name."</label></td>";
			$resultArr["resultStr"] .= "<td><input type='password' id='passw$id' /><span></span></td>";
			if ($user_adm_level)
				$resultArr["resultStr"] .=  "<td><input type='checkbox' id='admin$id' checked='checked' /><span></span></td>";
			else
				$resultArr["resultStr"] .=  "<td><input type='checkbox' id='admin$id'/><span></span></td>";
			$resultArr["resultStr"] .=  "<td><div class='type-button' style='margin: 0; '><input type='button' value='Save' onclick=\"saveUser('$id')\" /></div><span></span></td>";
			$resultArr["resultStr"] .=  "</tr>";
		}
		
		return json_encode($resultArr);
	}
	
	function getAuthInfo_json($userName)
	{
		$query = $this->dbh->query("SELECT last_key, last_key_date FROM users WHERE `user_name`='$userName'");
		$queryArr = $query->fetchAll();
		$resultArr = array();
		$resultArr['last_key'] = $queryArr[0][0];
		$resultArr['last_key_date'] = $queryArr[0][1];

		return json_encode($resultArr);
	}
	
	function verifyPassw($userName, $passw)
	{
		$query = $this->dbh->query("SELECT user_password FROM users WHERE `user_name`='$userName'");
		$queryArr = $query->fetchAll();
	    
		return (sha1($passw) == $queryArr[0]['user_password']);
	}
	
	function updateKey($userName, $newKey)
	{
		try
		{
			$this->dbh->exec("UPDATE users SET last_key='$newKey', last_key_date='".time()."' WHERE `user_name`='$userName'");
		}
		catch (PDOException $e)
		{
			echo "ERROR: Updating database with new key and time. Check the write permissions on the databasefile.<br />System Error Message: ".$e->getMessage();
		}
	}
	
	function isAdmin($userName)
	{
		$query = $this->dbh->query("SELECT user_admin_level FROM users WHERE `user_name`='$userName'");
		$queryArr = $query->fetchAll();
	    
		return ($queryArr[0]['user_admin_level'] == '1');
	}
	
	//------------------------------------------------------------ Retrieve Playlists --------------------------------------------------------
	function getPLContents($userName, $plNames)
	{
		$resultArr = array();
		$plStmt = $this->dbh->prepare("SELECT pl_contents FROM playlists WHERE `pl_user_name`=? AND `pl_name`=?");
		
		$pl = strtok($plNames, "|");
		while($pl !== false)
		{
			//print_r(array($userName, $pl));
			try
			{
				$plStmt->execute(array($userName, $pl));
			}
			catch(PDOException $e) { exit($e->getMessage()); }
			
			$queryArr = $plStmt->fetchAll();
			if (count($queryArr) != 0)
			{
				$resultArr = array_merge($resultArr, explode("|", $queryArr[0]["pl_contents"], -1));
			}		
			$pl = strtok("|");
		}
		//print_r($resultArr);
		return $resultArr;
	}
	
	function getPLsForUser_json($userName)
	{
		$resultArr = array();
			
		$query = $this->dbh->query("SELECT pl_name FROM playlists WHERE `pl_user_name`='$userName'");
		$queryArr = $query->fetchAll();
		//print_r($queryArr);
		
		for($i = 0; $i < count($queryArr); $i++)
		{
			//echo $queryArr[$i]["pl_name"]."\n\n";
			//echo htmlentities($queryArr[$i]["pl_name"])."\n\n";
			$resultArr[] = htmlentities($queryArr[$i]["pl_name"]);
		}
		
		return json_encode($resultArr);
	}
	
	function renamePL($name, $newNameP)
	{
		$newName = str_replace('|', '', $newNameP);
		try
		{
			$this->dbh->exec("UPDATE playlists SET `pl_name`='$newName' WHERE `pl_name`='$name'");
		}
		catch(PDOException $e)
		{
			echo "ERROR: Renaming playlist \"$name\" to \"$newName\"\n";
			echo $e->getMessage();
			return false;
		}
		return true;
	}
	
	function deletePL($userName, $plName)
	{
		try
		{
			$this->dbh->exec("DELETE FROM playlists WHERE `pl_name`='$plName' AND `pl_user_name`='$userName'");
		}
		catch(PDOException $e)
		{
			echo "ERROR: Deleting playlist \"$plName\"\n";
			echo $e->getMessage();
			return false;
		}
		return true;
	}
	
	function updatePL($plName, $newContent, $concat)
	{
		try
		{
			if ($concat == "true")
				$this->dbh->exec("UPDATE playlists SET pl_contents=pl_contents || '$newContent' WHERE `pl_name`='$plName'");
			else
				$this->dbh->exec("UPDATE playlists SET pl_contents='$newContent' WHERE `pl_name`='$plName'");
		}
		catch(PDOException $e)
		{ 
			echo $e->getMessage();
			return false;
		}
		return true;
	}
	
	function createPlaylist($userName, $plName, $plContent)
	{
		try
		{
			$this->dbh->exec("INSERT INTO playlists(pl_name, pl_contents, pl_user_name) VALUES ('$plName', '$plContent', '$userName')");
		}
		catch(PDOException $e)
		{
			if ($e->getCode() == 23000)
				echo "Playlist \"$plName\" already exists. Please enter a different name.\n";
			else
			{
				echo "ERROR: Creating the playlist \"$plName\": \n";
				echo $e->getMessage();
			}
			return false;
		}
		return true;
	}
}
?>
