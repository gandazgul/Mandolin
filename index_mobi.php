<?php
	session_name("Mandolin");	
	session_start();

	if (!isset($_SESSION["id"]) or ($_SESSION["id"] != sha1(session_id())))
	{
		include("login.php");
		exit();
	}
?>
<html>
	<head>
		<title>Mandolin <?php echo $settings['version']; ?></title>
	</head>
	<body>
		<p>Mandolin <?php echo $settings['version']; ?></p>
		<div id="plList">
			<?php
				$userName = $_SESSION["username"];
				$resultArr = array();
				$dbh = new PDO("sqlite:./db/users.db");
			
				$query = $dbh->query("SELECT pl_name FROM playlists WHERE `pl_user_name`='$userName'");
				$queryArr = $query->fetchAll();
				
				for($i = 0; $i < count($queryArr); $i++)
				{
					echo "<a href='./ls.php?a=play&for=bb&pl=".$queryArr[$i]["pl_name"]."'>".$queryArr[$i]["pl_name"]."</a><br />\n";
				}
				
				$dbh = null;
			?>
		</div>
	</body>
</html>
