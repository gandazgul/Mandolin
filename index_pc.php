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
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Mandolin v<?php echo $settings->get("version"); ?></title>
		<link rel="shortcut icon" href="./client/images/logo.png" />
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/redmond/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="./client/css/global/main.css" />
		<link rel='stylesheet' type='text/css' href='./client/css/<?php echo $p; ?>.css' />
		<!--[if IE]>
		<link href="./client/css/global/mod.ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
		<script type="text/javascript" src="./client/js/js.php?p=<?php echo $p; ?>"></script>
	</head>
	<body>
		<div class="page_margins">
			<div id="border-top">
				<div id="edge-tl"></div>
				<div id="edge-tr"></div>
			</div>
			<div class="page"><?php //echo $username; ?>
				<div id="header">
					<img alt="Mandolin logo" src="./client/images/logo.jpg" />
					<div id="headerTitle">
						<h1 id="appName">Mandolin v<?php echo $settings->get('version'); ?></h1>
						<h6 id="appSubName"><em>&ldquo;Because music is important&rdquo;</em></h6>
					</div>
					<?php if ($p != 'login'): ?>
						<div id="headerRight">
							<div id="topMenu">
								<a href="./?p=adm">Administration</a>&nbsp;|&nbsp;<a href="./?p=about">About</a>&nbsp;|&nbsp;<a href="./?p=logout">Logout</a>
							</div>
							<div id="searchForm">
								<input type="text" class="text" id="searchBox">
								<div class="search_button_wrapper">
									<div id="search_button"></div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>

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
					Copyright &copy; 2008-2010 SCTree (<a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv2</a>)
					&nbsp;|&nbsp;
					<a href="./?mobi=true">See mobile version</a>
				</div>
			</div>
			<div id="border-bottom">
				<div id="edge-bl"></div>
				<div id="edge-br"></div>
			</div>
		</div>
	</body>
</html>
