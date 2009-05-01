<?php
	session_name("newMusicServer");	
	session_start();
	//VERSION
	$fver = fopen("./version", "rt");
	$version = fgets($fver);
	fclose($fver);
	//VERSION END

	$p = (isset($_GET["p"])) ? $_GET["p"] : "main";
	if (isset($_SESSION["id"]))
		$p = ($_SESSION["id"] != sha1(session_id())) ? "login" : $p;
	else
		$p = "login";
	$sess_id = session_id();

	
	//print_r($_SESSION);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>newMusicServer v<?php echo $version; ?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<!-- add your meta tags here -->
	
	<link href="css/my_layout.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 7]>
	<link href="css/patches/patch_my_layout.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<script type="text/javascript" src="./js/lib/jquery-1.3.min.js"></script>
	<script type="text/javascript">
		<?php echo "SID = '".sha1(session_id())."';\n"; ?>
		function search(query)
		{
			query = query.value;
			
		}
		
		function showComments(comm)
		{
			if (comm != 'null')
				$("#sngComm").val(comm);
		}
		
		function albOnClick(data)
		{
			$("#songList")[0].options.length = 0;
			for (i = 0; i < data.length; i++)
			{
				$("#songList").append("<option value="+ data[i].id +" onclick='showComments(\""+ data[i].comm +"\")'>"+ data[i].name +"</option>");	
			}			
		}
		
		function _albOnClick(option)
		{
			postData = "a=sng&alb=" + option.value + "&SID=" + SID;
			$.post("./ls.php", postData, albOnClick, "json");
		}
		
		function artOnClick(data)
		{
			$("#albList")[0].options.length = 0;
			$("#songList")[0].options.length = 0;
			for (i = 0; i < data.length; i++)
			{
				$("#albList").append("<option value="+ data[i].id +" onclick='_albOnClick(this)'>"+ data[i].name +"</option>");	
			}
		}
		
		function _artOnClick(option)
		{
			postData = "a=alb&artist=" + option.value + "&SID=" + SID;
			$.post("./ls.php", postData, artOnClick, "json");
		}
		
		function getArt(data)
		{
			$("#artList")[0].options.length = 0;
			for (i = 0; i < data.length; i++)
			{
				$("#artList").append("<option value="+ data[i].id +" onclick='_artOnClick(this)'>"+ data[i].name +"</option>");	
			}
		}
		
		function _getArt()
		{
			postData = "a=art&SID=" + SID;
			$.post("./ls.php", postData, getArt, "json");
		}
		
		<?php 
			if ($p == "main")
			{
				echo "$(document).ready(function(){ _getArt(); });";
			}
		?>
	</script>
</head>
<body>
  <div class="page_margins">  	
    <div class="page">
      <div id="header">    	
		<img alt="newMusicServer logo" src="logo.jpg" />
		<div style="position: absolute; top: 10px; left: 200px">
			<?php
				$username = (isset($_SESSION['username'])) ? $_SESSION['username'] : "guest";

				echo "<h1>Welcome <strong>{$username}</strong> to newMusicServer v{$version}</h1>\n";	
			?>
			<h2><em>"Because music is important"</em></h2>
		</div>
      </div>
		<?php 
			include("{$p}.php");
		?>
      <!-- begin: #footer -->
      <div id="footer">
      	<a href="http://www.gnu.org/licenses/gpl.html">(L)</a> 2009 SCTree | <a href="./index.php?p=src">Get the code</a> | Layout based on <a href="http://www.yaml.de/">YAML</a>
      </div>
    </div>
  </div>
</body>
</html>