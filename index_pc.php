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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>Mandolin v<?php echo $settings->get("version"); ?></title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="./client/images/logo.ico" />
		<link href="./client/css/global/main.css" rel="stylesheet" type="text/css" />
		<link href="./client/css/lib/jquery-ui-1.8.custom.css" rel="stylesheet" type="text/css" />
		<link href='./client/css/<?php echo $p; ?>.css' rel='stylesheet' type='text/css' />
		<!--[if IE]>
		<link href="./client/css/global/mod.ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript" src="./client/js/lib/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="./client/js/lib/jquery-ui-1.8.custom.min.js"></script>
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
						<h2><em>&ldquo;Because music is important&rdquo;</em></h2>
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
						$page = "./client/".$mainPage.".php";
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
