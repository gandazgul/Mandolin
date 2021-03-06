<?php
require_once dirname(__FILE__).'/settings.php';
require_once dirname(__FILE__).'/result.php';

class UsersModel
{
	private $dbh;
	private $result;
	
	function __construct()
	{
		global $settings;

		$this->result = new Result();

		try
		{
			//$this->dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
			$this->dbh = new PDO($settings->get("dbDSN"));
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e)
		{
			$this->result->isError = true;
			$this->result->errorCode = $e->getCode();
			$this->result->errorStr = $e->getMessage();
		}
	}
	
	function __destruct()
	{
		unset($this->dbh);
		unset($this->result);
	}
	
	//-------------------------------------------------------------- User functions ----------------------------------------------------------
	function listUsers()
	{
		$query = $this->dbh->query("SELECT user_name, user_admin_level, user_id FROM users");
		return $query->fetchAll();
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
			$this->result->isError = true;
			$this->result->errorStr = "ERROR: Saving user information: ".$e->getMessage();
			$this->result->errorCode = $e->getCode();
		}
		
		$this->result->data = "User information saved successfully";
		return $this->result;
	}
	
	function addNewUser($user_name, $user_passw, $user_adm_level)
	{
		$this->resultArr["isError"] = false;
		
		$user_adm_level = (strtolower($user_adm_level) == "true") ? 1 : 0;
		$user_passw = (strlen($user_passw) == 40) ? $user_passw : sha1($user_passw);//I am asumming that is the password is 40 chars long its already sha1
		try
		{
			$result = $this->dbh->exec("INSERT INTO users(user_name, user_password, user_admin_level) VALUES ('$user_name', '$user_passw', $user_adm_level)");
			if ($result == 0)
			{
				$error = $this->dbh->errorInfo();
				$this->resultArr["isError"] = true;
				$this->resultArr["resultStr"] = "FATAL ERROR: While creating a new user: ".$error[2];
			}
			else
			{
				$result = $this->dbh->query("SELECT max(user_id) FROM users");
				$result = $result->fetchAll();
				$id = $result[0][0];
				$result['user_id'] = $id;
				$result['user_name'] = $user_name;
				$result['user_password'] = $user_passw;
				$result['user_admin_level'] = $user_adm_level;
				$this->resultArr["resultStr"] = $result;
			}
		}
		catch (PDOException $e)
		{
			$this->resultArr["isError"] = true;
			$this->resultArr["resultStr"] = $e->getMessage();
		}

		return json_encode($this->resultArr);
	}
	
	function deleteUser($id)
	{
		$this->resultArr['isError'] = false;
		
		$result = $this->dbh->exec("DELETE FROM users WHERE user_id=$id");
		if ($result == 0)
		{
			$this->resultArr['isError'] = true;
			$error = $this->dbh->errorInfo();
			$this->resultArr['resultStr'] = "ERROR: Couldn't delete the specified user($id): ".$error[2];
		}
		else
			$this->resultArr['resultStr'] = $id;
			
		return json_encode($this->resultArr);
	}
	
	function getAuthInfo_json($userName, $key = "")
	{
		$this->resultArr['isError'] = false;
		
		if ($key != "")
			$query = $this->dbh->query("SELECT last_key, last_key_date FROM users WHERE last_key='$key'");
		else
			$query = $this->dbh->query("SELECT last_key, last_key_date FROM users WHERE user_name='$userName'");
		
		$queryArr = $query->fetchAll();
		if (count($queryArr) == 0)
		{
			$this->resultArr['isError'] = true;
			$error = $this->dbh->errorInfo();
			$this->resultArr['resultStr'] = "ERROR: Retrieving user info: ".$error[2];
		}
		else
		{
			$this->resultArr['resultStr']['last_key'] = $queryArr[0][0]; 
			$this->resultArr['resultStr']['last_key_date'] = $queryArr[0][1];
		}
		
		return json_encode($this->resultArr);
	}
	
	function verifyPassw($userName, $passw)
	{
		if (($userName == "") or ($passw == ""))
			return false;
			
		$query = $this->dbh->query("SELECT user_password FROM users WHERE user_name='$userName'");
		$queryArr = $query->fetchAll();
	    if (count($queryArr) == 0)
	    	return false;
	    
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
		$query = $this->dbh->query("SELECT user_admin_level FROM users WHERE user_name='$userName'");
		$queryArr = $query->fetchAll();
	    //echo "Admin level: ".$queryArr[0]['user_admin_level'];
		return ($queryArr[0]['user_admin_level'] == '1');
	}
	
	function saveSettings($userName, $data)
	{
		if ($data == "")
		{
			$this->result->isError = true;
			$this->result->errorStr = "WARNING: You provided no settings to save";
		}
		else
		{
			$queryArr = $this->dbh->query("SELECT user_settings FROM users WHERE user_name='$userName'");
			$queryArr = $queryArr->fetchAll();
			if (count($queryArr) == 0)
			{
				$this->result->isError = true;
				$error = $this->dbh->errorInfo();
				$this->result->errorStr = "ERROR: Retrieving the user settings from the database: ".$error[2];
			}
			else
			{
				$settings = json_decode($queryArr[0]['user_settings'], true);
				$dataArr = json_decode($data, true);
				//print_r($dataArr);
				for ($i = 0; $i < count($dataArr['keys']); $i++)
				{
					$settings[$dataArr['keys'][$i]] = $dataArr['values'][$i];
				}
				$settings = json_encode($settings);
				//echo $settings;
				$result = $this->dbh->exec("UPDATE users SET user_settings='$settings' WHERE user_name='$userName'");
				if ($result == 0)
				{
					$this->result->isError = true;
					$error = $this->dbh->errorInfo();
					$this->result->errorStr = "ERROR: Saving the user settings to the database: ".$error[2];
				}
				else
				{
					$this->result->data = "Settings saved successfully";
				}
			}
		}
		
		return $this->result;
	}
	
	function loadSettings($userName, $keysArr, $key = "")
	{
		$this->resultArr['isError'] = false;
		$this->resultArr['resultStr'] = array();
		
		if (($userName == "") and ($key == ""))
		{
			$this->resultArr['isError'] = true;
			$this->resultArr['resultStr'] = "ERROR: Both User name and Key cannot be blank.";
		}
		else
		{		
			if ($userName == "")
				$queryArr = $this->dbh->query("SELECT user_settings FROM users WHERE last_key='$key'");
			else
				$queryArr = $this->dbh->query("SELECT user_settings FROM users WHERE user_name='$userName'");
				
			$queryArr = $queryArr->fetchAll();
			if (count($queryArr) == 0)
			{
				$this->resultArr['isError'] = true;
				$error = $this->dbh->errorInfo();
				$this->resultArr['resultStr'] = "ERROR: Retrieving the user settings from the database: ".$error[2];
			}
			else
			{
				$settingsArr = json_decode($queryArr[0]['user_settings'], true);
				//print_r($settingsArr);
				for ($i = 0; $i < count($keysArr); $i++)
				{
					$key = $keysArr[$i];
					//echo $key;
					$this->resultArr['resultStr'][$key] = $settingsArr[$key];
				}
			}
		}
		
		return json_encode($this->resultArr);
	}
}
?>
