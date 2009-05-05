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
	<!--script type="text/javascript" src="./js/lib/jquery-ui-1.7.1.custom.min.js"></script>
	<link type="text/css" rel="stylesheet" href="./css/jquery-ui-1.7.1.custom.css"-->
	<script type="text/javascript">
		<?php echo "SID = '".sha1(session_id())."';\n"; ?>
		
		function setComm()
		{
			alb_id = getOptions($("#albList")[0]);
			postData = "a=addc&com=" + $("#sngComm").val() + "&sng=" + $("#sngID").text() + "&alb=" + alb_id + "&SID=" + SID;
			//alert(postData);
			$.post("./ls.php", postData, albOnChange, "json");
		}
		
		function procSearchResults(results)
		{
			getArt(results["art"]);
			artOnChange(results["alb"]);
			albOnChange(results["sng"]);
		}
		
		function queryDB(query)
		{
			if (query == "")
			{
				_getArt();
				$("#albList")[0].options.length = 0;
				$("#songList")[0].options.length = 0;
				return;
			}
			postData = "a=search&q=" + query + "&SID=" + SID;
			$.post("./ls.php", postData, procSearchResults, "json");
		}
		
		var timerID;
		
		function search(query, reschedule)
		{
			if (reschedule)
			{
				//alert(query);
				clearTimeout(timerID);
				timerID = setTimeout("search('" + query + "', false)", 1000);
			}
			else
			{
				queryDB(query);
			}	
		}
		
		function getOptions(objSelect)//get all selected options in a <select> and separate them with |
		{
			txt = "";
			for (i = 0; i < objSelect.length; i++)
			{
			  if(objSelect.options[i].selected)
			  {
				//alert(encodeURI(obj.options[i].value));
				txt = txt + escape(objSelect.options[i].value) + "|";
			  }
			}
			return txt;
		}
		
		function sngOnChange(sng_value)
		{
			$("#sngComm").val("");
			$("#sngID").html("");
			data = eval('('+ sng_value +')');
			//alert(data[1]);
			if (data[1] != null) 
			{
				$("#sngComm").val(data[1]);
			}
			$("#sngID").append(data[0]);
		}
		
		function albOnChange(sngArr)
		{
			$("#songList")[0].options.length = 0;
			for (i = 0; i < sngArr.length; i++)
			{
				$("#songList").append("<option value='["+ sngArr[i].id + ", \"" + sngArr[i].comm + "\"]'>"+ sngArr[i].name +"</option>");	
			}			
		}
		
		function _albOnChange(objSelect)
		{
			alb_id = getOptions(objSelect);
			postData = "a=sng&alb=" + alb_id + "&SID=" + SID;
			$.post("./ls.php", postData, albOnChange, "json");
		}
		
		function artOnChange(albArr)
		{
			$("#albList")[0].options.length = 0;
			$("#songList")[0].options.length = 0;
			//alert(albArr[0].id);
			for (i = 0; i < albArr.length; i++)
			{
				$("#albList").append("<option value='"+ albArr[i].id +"'>"+ albArr[i].name +"</option>");	
			}
		}
		
		function _artOnChange(objSelect)
		{
			art_id = getOptions(objSelect);
			postData = "a=alb&artist=" + art_id + "&SID=" + SID;
			//alert(postData);
			$.post("./ls.php", postData, artOnChange, "json");
		}
		
		function getArt(artArr)
		{
			$("#artList")[0].options.length = 0;
			for (i = 0; i < artArr.length; i++)
			{
				$("#artList").append("<option value='"+ artArr[i].id +"'>"+ artArr[i].name +"</option>");	
			}
		}
		
		function _getArt()
		{
			postData = "a=art&SID=" + SID;
			$.post("./ls.php", postData, getArt, "json");
		}
		
		function putTotals(data)
		{
			$("#artTotal").html(data[0]);
			$("#albTotal").html(data[1]);
			$("#sngTotal").html(data[2]);
		}
		
		$(document).ready(function(){
			postData = "a=gett&SID=" + SID;
			$.post("./ls.php", postData, putTotals, "json");
		});
		
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
  	<div id="border-top">
      <div id="edge-tl"></div>
      <div id="edge-tr"></div>
    </div>
    <div class="page">
      <div id="header">    	
		<img alt="newMusicServer logo" src="logo.jpg" />
		<div style="position: absolute; top: 10px; left: 200px">
			<?php
				$username = (isset($_SESSION['username'])) ? $_SESSION['username'] : "guest";

				echo "<h1>Welcome <strong>{$username}</strong> to newMusicServer v{$version}</h1>\n";	
			?>
			<h2><em>"Because music is important"</em></h2>
			<br />
			<p class="title">Artists: <span id="artTotal"></span>,&nbsp;
			Albums:  <span id="albTotal"></span>,&nbsp;
			Songs:  <span id="sngTotal"></span></p>
		</div>	
      </div>

		<?php 
			include("{$p}.php");
		?>

      <!-- begin: #footer -->
      <div id="footer">
      	<a href="http://www.gnu.org/licenses/gpl.html">(L)</a> 2009 SCTree | <a href="./src.php">Get the code</a> | Layout based on <a href="http://www.yaml.de/">YAML</a>
      </div>
    </div>
    <div id="border-bottom">
      <div id="edge-bl"></div>
      <div id="edge-br"></div>
    </div>	
  </div>
</body>
</html>