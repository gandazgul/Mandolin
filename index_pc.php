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
		<link href="./client/css/global/main.css" rel="stylesheet" type="text/css" />
		<link href="./client/css/lib/jquery-ui-1.8.custom.css" rel="stylesheet" type="text/css" />
		<link href='./client/css/<?php echo $p; ?>.css' rel='stylesheet' type='text/css' />
		<!--[if IE]>
		<link href="./client/css/global/mod.ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<script type="text/javascript" src="./client/js/js.php?p=<?php echo $p; ?>"></script>
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
						<h2>Welcome <strong><?php echo $username; ?></strong> to Mandolin v<?php echo $settings->get('version'); ?></h2>
						<h3><em>&ldquo;Because music is important&rdquo;</em></h3>
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
					Copyright &copy; 2009 SCTree (<a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPLv2</a>)
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
