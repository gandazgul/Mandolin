<?php
$sess_id = "to fool createDB";
require_once '../models/Settings.php';


$step = ($_GET["step"] == "") ? 1 : $_GET["step"];
?>
	<script language="javascript" type="text/javascript">
		function AddUser(strUser, strPassword, rePassw)
		{
		//alert(strUser);
			if ( (strPassword == "") || (rePassw == "") )
				alert("ERROR: Passwords can't be empty");
			else	
			if ( strPassword == rePassw )
				return true;
			else
				alert("ERROR: Passwords don't match");
				
			return false;
		}
	</script>
	<div align="center">
		Thanks for downloading SCTree Mandolin v<?php echo $settings->get("version") ?><br />
		Installation - Step <?php echo $step; ?>
	</div><br />
<?php
if ($step == 1)
{
	include("inst_step1.php");
}
else
if ($step == 2)
{	
	echo "Creating user database...";
	if ( !(is_dir("../db") || mkdir("../db", 0770)) )
		die("<font color=\"red\">FATAL ERROR: Can't create databases directory. Please create a dir inside this one named \"db\" and make it writable; then restart the installation.</font>");
	fclose(fopen("../db/index.php", "wt"));
	if (file_exists($usersDB))
		if ( !unlink($usersDB) )
			die("<font color=\"red\">FATAL ERROR: Can't delete the users database(\"$usersDB\") file. If this is the first installation that file should'nt exist. Please delete it manually.</font>");

  	$dbh = new PDO("sqlite:$usersDB");
    $result = $dbh->exec(file_get_contents("./users.db.sql"));
	if ($result === false)
	{
		die("<font color=\"red\">FATAL ERROR: Creating tables in the users database. Error Info: ".implode(" ", $dbh->errorInfo())."</font>");
	}
	$dbh = null;
	echo "<font color=\"green\">DONE</font><br /><br />\nPlease enter the user name you wish to use and a password for it below. NOTE: This user is created as an administrator.<br /><br />\n";
	?>
	<form action="./index.php?step=3" method="post" onsubmit="return AddUser($('first_user').value, $('first_passw').value, $('first_rePassw').value)">
		Username:&nbsp;<input type="text" name="first_user" id="first_user" /><br />
		Password:&nbsp;<input type="text" name="first_passw" id="first_passw" /><br />
		Repeat password:&nbsp;<input type="text" name="first_rePassw" id="first_rePassw" /><br /><br />
		<input type="submit" value="Add User" />
	</form>
<?php
}
else
if ($step == 3)
{
	$first_user = $_POST["first_user"];
	$first_passw = sha1($_POST["first_passw"]);
	$last_key_date = time();
	$last_key = sha1($first_user."@".$first_passw.":".$last_key_date);
  	$dbh = new PDO("sqlite:$usersDB");	

	$dbh->exec("INSERT INTO users(user_name, user_password, user_admin_level, last_key, last_key_date) VALUES ('$first_user', '$first_passw', 0, '$last_key', $last_key_date)") or
		die("<font color=\"red\">FATAL ERROR: While adding a new user to the database. ".implode(" ", $dbh->errorInfo())."</font>");
	$dbh = null;
	
	echo "<form action='./index.php?step=4' method='post'>User successfully added, <input type='submit' value='Continue to Step 4 >' /></form>";
}
else
if ($step == 4)
{
	$musicURL =  "http://".$_SERVER['HTTP_HOST'];
	$musicURL .= substr($_SERVER['PHP_SELF'], 0, -10);
?>
	<form action='./index.php?step=5' method='post'>
		Please check and correct if necessary the address to the server. This will be used to create playlists and will be kept in the file: "settings" under: "mstURL=";<br /><br />
		<input name="mstURL" value="<?php echo $musicURL; ?>" />
		<input type='submit' value='Step 5 >' />
	</form>
<?php
}
else
if ($step == 5)
{
	$settings["mstURL"] = $_POST[mstURL];
?>
	<form action='./index.php?step=6' method='post'>
		<strong>Where is the music?</strong><br />
		Please enter the folder where you keep your music then press "Add to DB"<br /><br />
		<input name="musicRoot" value="" />
		<input type='submit' value='Add to DB > Step 6 >' />
	</form>
<?php
}
else
if ($step == 6)
{
	//add the root to the settings file
	$musicRoot = str_replace("\\", "/", $_POST["musicRoot"]);
	$settings["musicRoot"] = $musicRoot;
	
	file_put_contents($setFile, json_encode($settings));
	
	//get the key
	$dbh = new PDO("sqlite:../db/users.db"); //this code can be merged with the same one on login.php
		$query = $dbh->query("SELECT last_key, user_name FROM users");
		$queryArr = $query->fetchAll();
		$_SESSION["key"] = $queryArr[0][0];
		$_SESSION["username"] = $queryArr[0][1];
		$_SESSION["userAdminLevel"] = 0;
	$dbh = null;
	//call createDB
	include("../createDB.php");

	echo "Congatulations! Installation is complete. Please proceed to the login page. <a href=\"../index.php\">Login</a>";
}
?>