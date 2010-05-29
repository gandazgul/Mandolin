<div id="main">
	<?php
	$step = (isset ($_GET["step"])) ? $_GET["step"] : 1;
	?>
		<p style="margin-top: 10px" class="title">Installation - Step <?php echo $step; ?></p>
	<?php
	if ($step == 1)
	{
		include("./install/step1.php");
	}
	else
	if ($step == 2)
	{
		echo "Creating database...";
		if ( !(is_dir("./data") or mkdir("./data", 0770)) )
			die("<font color=\"red\">FATAL ERROR: Can't create data directory. Please create a dir inside this one named \"data\" and make it writable; then reload this page.</font>");
		if (!file_exists("./data/index.php"))
			fclose(fopen("./data/index.php", "wt"));

		require_once './models/settings.php';

		$settings->set("baseDir", realpath(".").DIRECTORY_SEPARATOR);
		$settings->set("dbDSN", "sqlite:".$settings->get("baseDir")."data/mandolin.db");
		$settings->set("dbUser", "null");
		$settings->set("dbPassword", "null");

		if (file_exists("./data/mandolin.db"))
			echo "Database file exist I will asume this is an upgrade and you want to keep it.";
		else
		{
			//$dbh = new PDO($settings->get("dbDSN"), $settings->get("dbUser"), $settings->get("dbPassword"), array(PDO::ATTR_PERSISTENT => true));
			$dbh = new PDO($settings->get("dbDSN"));
			$result = $dbh->exec(file_get_contents("./install/mandolin.sql"));
			if ($result === false)
			{
				die("<font color=\"red\">FATAL ERROR: Creating tables in the database. Error Info: ".implode(" ", $dbh->errorInfo())."</font>");
			}
			$dbh = null;

			echo "<font color=\"green\">DONE</font><br /><br />\nAdministrator user created.<br /><br />\n";
			?>
		<?php
		}
		echo "<form action='./?p=install&step=3' method='post'><input class='ui-state-default ui-corner-all' type='submit' value='Continue to Step 3 >' /></form>";
	}
	else
	if ($step == 3)
	{
		$baseURL =  "http://".$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'], 0, -10);
	?>
		<form action='./index.php?step=4' method='post'>
			Please check and correct if necessary the address to the server. This will be used to create playlists and will be kept in the file: "settings.json" under the key: "baseURL";<br /><br />
			<input name="baseURL" value="<?php echo $baseURL; ?>" size="40" />
			<input type='submit' value='Step 4 >' class="ui-state-default ui-corner-all" />
		</form>
	<?php
	}
	else
	if ($step == 4)
	{
		$settings->set("baseURL", $_POST["baseURL"]);
		echo "baseURL was set<br /><br />";
		echo "Congratulations! Installation is complete. Remember to delete the install folder.<br /><br />";
		echo "username: admin<br/>password: admin<br/><br/>Remember to change this password or create another user and delete this one.<br /><br /><a href='.'>Login Page</a>";
	}
	?>
</div>