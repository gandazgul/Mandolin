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
	echo "Checking database...";
	if ( !(is_dir("../data") or mkdir("../data", 0770)) )
		die("<font color=\"red\">FATAL ERROR: Can't create data directory. Please create a dir inside this one named \"data\" and make it writable; then reload this page.</font>");
	fclose(fopen("../data/index.php", "wt"));
	
	if (file_exists("../data/mandolin.db"))
		echo "Database file exist I will asume this is an upgrade and you want to keep it.";
	else
	{
		//$dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
		$dbh = new PDO($settings->get("dbDSN"));
		$result = $dbh->exec(file_get_contents("./mandolin.sql"));
		if ($result === false)
		{
			die("<font color=\"red\">FATAL ERROR: Creating tables in the database. Error Info: ".implode(" ", $dbh->errorInfo())."</font>");
		}
		$dbh = null;
		echo "<font color=\"green\">DONE</font><br /><br />\nPlease enter the user name you wish to use and a password for it below. NOTE: This user is created as an administrator.<br /><br />\n";
	}
	echo "<form action='./index.php?step=3' method='post'><input type='submit' value='Continue to Step 3 >' /></form>";
}
else
if ($step == 3)
{
	$baseURL =  "http://".$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, -18);
?>
	<form action='./index.php?step=4' method='post'>
		Please check and correct if necessary the address to the server. This will be used to create playlists and will be kept in the file: "settings.json" under the key: "baseURL";<br /><br />
		<input name="baseURL" value="<?php echo $baseURL; ?>" />
		<input type='submit' value='Step 4 >' />
	</form>
<?php
}
else
if ($step == 4)
{
	$settings->set("baseURL", $_POST["baseURL"]);
	echo "baseURL was set<br /><br />";
	echo "Congratulations! Installation is complete. Remember to delete the install folder.<br /><br />";
	echo "<a href='../?p=adm'>Administration Page</a>";
}
?>