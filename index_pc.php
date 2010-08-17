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
		<link type="text/css" rel="stylesheet" href="./client/css/lib/jplayer.blue.monday.css" />
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
					<div id="jplayer-player"></div>
					<?php if ($p != 'login'): ?>
						<div class="jp-single-player">
							<div class="jp-interface">
								<ul class="jp-controls">
									<li><a href="#" id="jplayer_play" class="jp-play" tabindex="1">play</a></li>
									<li><a href="#" id="jplayer_pause" class="jp-pause" tabindex="1">pause</a></li>
									<li><a href="#" id="jplayer_stop" class="jp-stop" tabindex="1">stop</a></li>
									<li><a href="#" id="jplayer_volume_min" class="jp-volume-min" tabindex="1">min volume</a></li>
									<li><a href="#" id="jplayer_volume_max" class="jp-volume-max" tabindex="1">max volume</a></li>
								</ul>
								<div class="jp-progress">
									<div id="jplayer_load_bar" class="jp-load-bar">
										<div id="jplayer_play_bar" class="jp-play-bar"></div>
									</div>
								</div>
								<div id="jplayer_volume_bar" class="jp-volume-bar">
									<div id="jplayer_volume_bar_value" class="jp-volume-bar-value"></div>
								</div>
								<div id="jplayer_play_time" class="jp-play-time"></div>
								<div id="jplayer_total_time" class="jp-total-time"></div>
							</div>
							<div id="jplayer_playlist" class="jp-playlist">
								<ul>
									<li>Track title</li>
								</ul>
							</div>
						</div>
						<div id="headerRight">
							<div id="topMenu">
								<a href="./?p=music">Home</a>&nbsp;|&nbsp;<a href="./?p=adm">Administration</a>&nbsp;|&nbsp;<a href="./?p=about">About</a>&nbsp;|&nbsp;<a href="./?p=logout">Logout</a>
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
