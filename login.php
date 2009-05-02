<?php
	if ((isset($_POST["username"])) and (!isset($sess_id)))
	{
		session_name("newMusicServer");
		session_start();
		$username = $_POST["username"];
		$passw = $_POST["passw"];
	    //echo $username;
	    //get the existing password
	    $dbh = new PDO("sqlite:./db/users.db");
	    $query = $dbh->query("SELECT user_password, last_key, last_key_date, user_admin_level FROM users WHERE `user_name`='$username'");
	    $queryArr = $query->fetchAll();
	    $t_passw = $queryArr[0][0];  //password
	    $last_key = $queryArr[0][1]; //last key stored
	    $last_key_date = $queryArr[0][2]; //last key date
	    //print_r($queryArr);
	    if ($t_passw == md5($passw))//if the passwords match
	    {
	    	//echo "<br/>last key date: $last_key_date<br/>";
			//echo "current date: ".time()."<br/>";
		    //86400 is the number of seconds in a day, this number should be a global variable (settings)
			//add the timestamp of creation for new users and eliminate the check if empty
			if (($last_key_date == "") or (time() - $last_key_date > 86400)) //we didnt find a key or the key is old lets create one.
			{
	            $last_key = sha1($username."@".$passw.":".time());
	            $result = $dbh->exec("UPDATE users SET last_key='$last_key', last_key_date='".time()."' WHERE `user_name`='$username'");
				if ($result == 0)
				{
				    die("Error updating database for new key and time. Check the write permissions on the database. Error Info: ".implode(" ", $dbh->errorInfo()));
				}
		        //echo "Result of Updating users: $result <br/>\n";
		        //print_r($dbh->errorInfo());
		        //echo "<br/>";
	            $dbh = null;
			}
			$_SESSION["key"] = $last_key;
			$_SESSION["username"] = $username;
			$_SESSION["userAdminLevel"] = $queryArr[0][3];
			$_SESSION["id"] = sha1(session_id());
			//print_r($_SESSION);
	        header("Location: ./index.php?p=main");
			exit();
	    }
		setcookie("SCTm", "Incorrect username or password. Please try again.");
	    header("Location: ./index.php");
	}
	else
	if (!isset($sess_id))
		header("Location: ./index.php");	
?>
<div id="main">
	<form action="./login.php" method="post">
		<p class="title">Login</p>
		<p class="title">Username: <input type="text" size="20" name="username" /></p>
		<p class="title">Password: <input type="password" size="20" name="passw" /></p>
		<input type="submit" value="Login" />
	</form>
	<?php 
		if (isset($_COOKIE["SCTm"])) 
		{
			echo "<p style='color: red; font: 'Courier New', Courier, mono;'>Error: ".$_COOKIE["SCTm"]."</p>"; 
			setcookie("SCTm", '', time()-42000, '/'); 
		} 
	?>	
</div>