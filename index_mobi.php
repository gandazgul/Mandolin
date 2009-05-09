<?php
	session_name("newMusicServer");	
	session_start();
	//VERSION
	$fver = fopen("./version", "rt");
	$version = fgets($fver);
	fclose($fver);
	//VERSION END

	if (!isset($_SESSION["id"]) or ($_SESSION["id"] != sha1(session_id())))
	{
		include("login.php");
		exit();
	}
?>
<html>
	<head>
		<title>newMusicServer <?php echo $version; ?></title>
	</head>
	<body>
		<p>newMusicServer <?php echo $version; ?></p>
		<div id="plList">
			<?php
				$userName = $_SESSION["username"];
				$resultArr = array();
				$dbh = new PDO("sqlite:./db/users.db");
			
				$query = $dbh->query("SELECT pl_name FROM playlists WHERE `pl_user_name`='$userName'");
				$queryArr = $query->fetchAll();
				
				for($i = 0; $i < count($queryArr); $i++)
				{
					echo "<a href='./ls.php?a=down&pl=".$queryArr[$i]["pl_name"]."'>".$queryArr[$i]["pl_name"]."</a><br />\n";
				}
				
				$dbh = null;
			?>
		</div>
	</body>
</html>