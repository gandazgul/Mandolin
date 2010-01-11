<?php
	session_name("Mandolin");	
	session_start();

	$p = (isset($_GET["p"])) ? $_GET["p"] : $settings->get("mainPage");
	if (isset($_SESSION["id"]))
		$p = ($_SESSION["id"] != sha1(session_id())) ? "login" : $p;
	else
		$p = "login";
	
	$page = "./client/$p.php";
	if (!file_exists($page))
		$page = $page = "./client/".$settings->get("mainPage").".php";
		
	$sess_id = session_id();

	//print_r($_SESSION);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Mandolin v<?php echo $settings->get("version"); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="./client/images/logo.ico" />
	<link href="./client/css/main.css" rel="stylesheet" type="text/css" />
	<link href="./client/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
	<link href="./client/css/mod.ie.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<script type="text/javascript" src="./client/js/lib/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="./client/js/lib/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript"><?php echo "SID = '".sha1(session_id())."';"; ?></script>
	<script type="text/javascript" src="./client/js/lib/main.js"></script>

	<?php
	echo "<link type='text/css' rel='stylesheet' href='./client/css/$p.css' />\n";
	echo "\t<script type='text/javascript' language='javascript' src='./client/js/$p.js'></script>\n";
	?>

</head>
<body>
	<div class="page_margins">
		<div id="border-top">
			<div id="edge-tl"></div>
			<div id="edge-tr"></div>
		</div>
		<div class="page">
			<div id="header">    	
				<img alt="Mandolin logo" src="./client/images/logo.jpg" />
				<div style="position: absolute; top: 10px; left: 200px">
					<?php
						$username = (isset($_SESSION['username'])) ? $_SESSION['username'] : "guest";
						
						echo "<h1>Welcome <strong>{$username}</strong> to Mandolin v".$settings->get('version')."</h1>\n";	
					?>
					<h2><em>"Because music is important"</em></h2>
				</div>	
			</div>
			<div id="nav">
				<!-- skiplink anchor: navigation -->
				<a id="navigation" name="navigation"></a>
				<div class="hlist">
					<!-- main navigation: horizontal list -->
					<ul>
						<li><a href=".">Music</a></li>
						<li class="active"><strong>Music Playlists</strong></li>
						<li><a href="./?p=movies">Movies</a></li>
						<li><a href="./?p=adm">Aministration</a></li>
						<li><a href="./?p=about">About</a></li>
						<li><a href="./client/logout.php">Logout</a></li>
					</ul>
				</div>
			</div>
			<div id="teaser">
				<div id="errorDiv" class="important"></div>
			</div>
			
			<?php include($page); ?>
			
			<!-- begin: #footer -->
			<div id="footer">
				<a href="http://www.gnu.org/licenses/gpl.html">(L)</a> 2009 SCTree | <a href="http://github.com/gandazgul/Mandolin" target="_bank">Get the code @GitHub</a> | Layout based on <a href="http://www.yaml.de/">YAML</a>
			</div>
		</div>
		<div id="border-bottom">
			<div id="edge-bl"></div>
			<div id="edge-br"></div>
		</div>	
	</div>
</body>
</html>
