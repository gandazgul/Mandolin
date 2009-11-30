<?php
	//print_r($_POST);
	if (isset($_POST["username"]))
	{
		require_once("./models/UsersDB.php");
		require_once("./models/Settings.php");

		$username = $_POST["username"];
		$passw = $_POST["passw"];
		//echo sha1($passw)."<br />\n";
		//echo "$username<br />\n";
	    $usersDB = new UsersDB("./models/dbfiles/users.db");
	    if ($usersDB->verifyPassw($username, $passw))//if the passwords match
	    {
			$authDataArr = json_decode($usersDB->getAuthInfo_json($username), true);
			$key = $authDataArr['last_key']; //last key stored
			$last_key_date = $authDataArr['last_key_date']; //last key date
	    	$settings = new Settings();
	    	//echo "<br/>last key date: $last_key_date<br/>";
			//echo "current date: ".time()."<br/>";
		    if (($last_key_date == "") or ((time() - $last_key_date) > $settings->get('keyLastsFor'))) //we didnt find a key or the key is old lets create one.
			{
	            $key = sha1($username."@".$passw.":".time());
	            $usersDB->updateKey($username, $key);
			}
			session_name("newMusicServer");
			session_start();
			session_regenerate_id();
			$_SESSION["key"] = $key;
			$_SESSION["username"] = $username;
			$_SESSION["userAdminLevel"] = $usersDB->isAdmin($username);
			$_SESSION["id"] = sha1(session_id());
			//print_r($_SESSION);
			header("Location: .");
			exit();
	    }
		header("Location: ../index.php?passw=false");
	}
	else
	if (is_dir("./install/")):?>
		<p style="margin-top: 10px" class="title">If this is the first time you access newMusicServer, then <a href="./install">click here to install</a>. 
		If you already completed the installation then, delete the "install" directory before trying to login.</p>
	<?php else: ?>
		<div id="main">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="yform">
				<fieldset>
					<legend>Please login</legend>
					<div class="type-text">
						<?php if(isset($_GET["passw"])):?>
							<strong class="message">ERROR: Incorrect Username and/or Password</strong>
						<?php endif; ?>
						<label for="username">Username:</label>
						<input type="text" size="20" name="username" id="username" />
					</div>
					<div class="type-text">
						<label for="passw">Password:</label>
						<input type="password" size="20" name="passw" id="passw" />
					</div>
				<div class="type-button">
					<input type="submit" value="Login" />
				</div>
				</fieldset>
			</form>
		</div>
	<?php endif; ?>