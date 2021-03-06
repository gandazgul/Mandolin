<?php

class CDefault //extends CController
{
	function  __construct()
	{

	}

	function  __destruct()
	{
		
	}

	function checkAuth()
	{
		global $settings;
		require_once("./models/users.php");

		$username = $_POST["username"];
		$passw = $_POST["passw"];
		//echo sha1($passw)."<br />\n";
		//echo "$username<br />\n";
		$mUsers = new UsersModel();
		if ($mUsers->verifyPassw($username, $passw))//if the passwords match
		{
			$authDataArr = json_decode($mUsers->getAuthInfo_json($username), true);
			if (!$authDataArr['isError'])
			{
				$key = $authDataArr['resultStr']['last_key']; //last key stored
				$last_key_date = $authDataArr['resultStr']['last_key_date']; //last key date
				//echo "<br/>last key date: $last_key_date<br/>";
				//echo "current date: ".time()."<br/>";
				if (($last_key_date == "") or ((time() - $last_key_date) > $settings->get('keyLastsFor'))) //we didnt find a key or the key is old lets create one.
				{
					$key = sha1($username."@".$passw.":".time());
					$mUsers->updateKey($username, $key);
				}
				//session_name("Mandolin");
				//session_start();
				session_regenerate_id();
				$_SESSION["key"] = $key;
				$_SESSION["username"] = $username;
				$_SESSION["userAdminLevel"] = $mUsers->isAdmin($username);
				$_SESSION["id"] = sha1(session_id());
				//print_r($_SESSION);
				header("Location: .");
				exit();
			}
		}
		header("Location: ./?p=login&passw=false");
	}

	function login()
	{?>
		<div id="main">
			<form action="./?p=checkAuth" method="post" id="login-form" class="ui-form">
				<fieldset class="ui-widget-content ui-corner-all">
					<?php if(isset($_GET["passw"])): ?>
					<strong class="info">ERROR: Incorrect Username and/or Password</strong>
					<?php endif; ?>
					<label for="username">Username:</label>
					<input type="text" size="20" name="username" id="username" class="text ui-widget-content ui-corner-all" />
					<label for="passw">Password:</label>
					<input type="password" size="20" name="passw" id="passw" class="text ui-widget-content ui-corner-all" />
				</fieldset>
				<fieldset class="ui-widget-content ui-corner-all top">
					<button type="submit" id="btnLogin">Login</button>
				</fieldset>
			</form>
		</div>
		<script type="text/javascript">
			$("#main").css('height', '180px');
			$('#btnLogin').button({
				icons:{
					primary: 'ui-icon-ok'
				}
			});
		</script>
		<style type="text/css">
			.ui-icon-ok { background-image: url("./client/images/accept.png") !important; }
		</style>
	<?php
	}

	function logout()
	{
		//session_name("Mandolin");
		//session_start();

		$_SESSION = array();
		unset($sess_id);

		if (isset($_COOKIE["Mandolin"]))
		{
			//setcookie($sName, '', time()-42000, '/');
			setcookie(session_name(), session_id(), 1, '/');
			session_destroy();
			header("Location: .");
		}
		else
			echo "There was a problem with the session handling. Reload the page.";
	}

	function createDB()
	{
		global $settings;
		
		if ($_SESSION['userAdminLevel'] != 1)
		{
			logout();
			exit();
		}
		ini_set('max_execution_time', '6000');

		require_once './models/music.php';
		$mMusic = new MusicModel();

		$mMusic->recreateDB();
		//---------------------------------------------NOW LET'S FILL THE DATABASE----------------------------------------
		?>
		<div id='teaser'>
			<div id='errorDiv' class="important"></div>
		</div>
		<div id='main'>
			<h1>Creating the Database</h1>
			<ul>
				<li>Database deleted and new one created</li>
				<li>Scanning directories to add music to the new DB - <span style='color: #CC3300; '>DO NOT HIT THE BACK BUTTON ON YOUR BROWSER!!!</span></li>
				<li><ul>
					<li>Artists: <span id='art'></span></li>
					<li>Albums:  <span id='alb'></span></li>
					<li>Songs: <span id='sng'></span></li>
				</ul></li>
				<li>-------------------------------------------</li>
				<?php
					//echo $settings->get("musicFolders");
					$folderArr = json_decode($settings->get("musicFolders"), true);
					//print_r($folderArr);
					for ($i = 0; $i < count($folderArr); $i++)
					{
						$curFolder = $folderArr[$i];
						$mMusic->addToDB($curFolder, strlen($curFolder));
						echo "<li>$curFolder - DONE</li>";
					}
				?>
			</ul>
		</div>
<?php
	}
}

?>
