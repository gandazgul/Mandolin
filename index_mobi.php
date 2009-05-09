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
	}
?>
<html>
	<head>
		<script type="text/javascript" src="js/lib/jquery-1.3.min.js"></script>
		<script type="text/javascript">	
			<?php echo "SID = '".sha1(session_id())."';\n"; ?>
					
			function getSavedPL(savedPLArr)
			{
				$("#plList").html("");
				for (i = 0; i < savedPLArr.length; i++)
				{
					$("#plList").append("<a href='./ls.php?a=down&pl=" + savedPLArr[i] + "'>" + savedPLArr[i] + "</a><br />");	
				}
			}
			
			function _getSavedPL()
			{
				postData = "a=saved&un=<?php if(isset($_SESSION["username"])) echo $_SESSION["username"]; ?>&SID=" + SID;
				$.post("./ls.php", postData, getSavedPL, "json");
			}
		</script>
	</head>
	<body onload="_getSavedPL()">
		<p>newMusicServer <?php echo $version; ?></p>
		<div id="plList"></div>
	</body>
</html>