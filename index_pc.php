<?php
	session_name("Mandolin");	
	session_start();
	$sess_id = session_id();

	if ($p != "install")
	{
		if ((!isset($_SESSION["id"]) or !($_SESSION["id"] == sha1($sess_id))) and ($p != "checkAuth"))
			$p = "login";
	}

	$username = (isset($_SESSION['username'])) ? $_SESSION['username'] : "guest";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Mandolin v<?php echo $settings->get("version"); ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="./client/images/logo.ico" />
	<link href="./client/css/main.css" rel="stylesheet" type="text/css" />
	<link href="./client/css/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" />
	<link href='./client/css/<?php echo $p; ?>.css' rel='stylesheet' type='text/css' />
	<!--[if IE]>
	<link href="./client/css/mod.ie.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<script type="text/javascript" src="./client/js/lib/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="./client/js/lib/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript"><?php echo "SID = '".sha1(session_id())."';"; ?></script>
	<script type="text/javascript" src="./client/js/main.js"></script>
	<script type='text/javascript' src='./client/js/<?php echo $p; ?>.js'></script>
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
				<div class="title">
					<h1>Welcome <strong><?php echo $username; ?></strong> to Mandolin v<?php echo $settings->get('version'); ?></h1>
					<h2><em>"Because music is important"</em></h2>
				</div>	
			</div>
			
			<?php include "./client/views/nav.php"; ?>

			<?php
			$page = "./client/$p.php";
			if (!file_exists($page))
			{
				include "./client/_default.php";
				$default = new CDefault();
				if (method_exists($default, $p))
				{
					$default->$p();
				}
				else
				{
					$page = "./client/nav.".$mainPage.".php";
					include($page);
				}
				unset($default);
			}
			else
			{
				include($page);
			}
			?>
			
			<!-- begin: #footer -->
			<div id="footerHead">&nbsp;</div>
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
