<?php
	session_name("Mandolin");	
	session_start();

	if (!isset($_SESSION["id"]) or ($_SESSION["id"] != sha1(session_id())))
	{
		$sess_id = "Mandolin";//to fool the security in login.php
		include("./client/login.php");
		exit();
	}
	
	$v = $settings->get('version');
?>
<html>
	<head>
		<title>Mandolin <?php echo $v; ?></title>
	</head>
	<body>
		<center>
			<p>Mandolin <?php echo $v; ?></p>
			<p><img src="./client/images/logo.jpg" alt="Mandolin" /></p>
			<div id="plList">
				<?php
					$userName = $_SESSION["username"];
					include_once './models/playlists.php';
					$mPlaylist = new PlaylistsModel($userName);
					
					$resultArr = $mPlaylist->get(null);
					//print_r($resultArr);
					for($i = 0; $i < count($resultArr); $i++)
					{
						echo "<a href='./server/music.php?a=play&pl_id=".$resultArr[$i]['id']."&SID=".$_SESSION["id"]."'>".$resultArr[$i]['name']."</a><br />\n";
					}
					
					unset($mPlaylist);
				?>
			</div>
		</center>
	</body>
</html>
