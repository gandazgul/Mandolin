<?php
if (!isset($sess_id) or (($_SESSION["userAdminLevel"] != 0) and ($_SESSION["userAdminLevel"] != 1))):?>
	
	<script type="text/javascript">
		alert('You don\'t have permission to update the database. Ask an administrator to do it.');
	</script>
	<?php
	header("Location: ./index.php");
	exit();
endif;

ini_set('max_execution_time','6000');

$dbh = new PDO("sqlite:./db/music.db");

//get the totals +1 to use them as ids for new stuff
$query = $dbh->query("SELECT max(art_id), count(art_id) FROM artists");
$queryArr = $query->fetchAll();
$atrMax = $queryArr[0][0] + 1;
$artCount = $queryArr[0][1];
$query = $dbh->query("SELECT max(alb_id), count(alb_id) FROM albums");
$queryArr = $query->fetchAll();
$albMax = $queryArr[0][0] + 1;
$albCount = $queryArr[0][1];
$query = $dbh->query("SELECT max(song_id), count(song_id) FROM music");
$queryArr = $query->fetchAll();
$sngMax = $queryArr[0][0] + 1;
$sngCount = $queryArr[0][1];

$fset = fopen("./settings", "rt");
fgets($fset);//musicURL
$root = fgets($fset);
$root = substr($root, strpos($root, "=") + 1, -1);
if (substr($root, -1) != "/")
	$root .= "/";
fclose($fset);

//fucntions
function processArtDir($root, $art_id)//process the artist directory looking for the album folders.
{
    global $dbh, $albCount, $albMax;

    $dirH = opendir($root);
    if (!$dirH)
      die("FATAL ERROR: Can't read directory: $root");
    while (($file = readdir($dirH)) !== false)
    {
        if (($file == ".") or ($file == ".."))
            continue;
        if (is_dir($root.$file))
        {
            $dir = $root.$file."/";
            $file = str_replace("'", "''", $file);
            $query = $dbh->query("SELECT alb_id FROM albums WHERE `alb_name`='$file' AND `alb_art_id`='$art_id'");
            $queryArr = $query->fetchAll();
            if ($queryArr[0][0] == "")
            {
                $dbh->exec("INSERT INTO albums(alb_id, alb_name, alb_art_id) VALUES ('$albMax', '$file', '$art_id')") or
					die("FATAL ERROR: Inserting: $file into albums with ID: $albMax\n".implode(" ", $dbh->errorInfo()));
                $alb_id = $albMax;
				$albMax++;
                $albCount++;
				echo "<script language=\"javascript\">document.getElementById('alb').innerHTML = $albCount;</script>";
				flush(); ob_flush();
            }
            else
            {
                $alb_id = $queryArr[0][0];
            }
            processAlbDir($dir, $art_id, $alb_id);
        }
        else
        {
            //process here songs that are alone in the artists folder and add them to an unknown album
        }
    }
    closedir($dirH);
}

function processAlbDir($root, $art_id, $alb_id)//process the album folder looking for songs
{
    global $dbh, $sngCount, $sngMax;

    $dirH = opendir($root);
	if(!$dirH)
  		die ("FATAL ERROR: Can't read directory: $root");
    while (($file = readdir($dirH)) !== false)
    {
        if (($file == ".") or ($file == ".."))
            continue;
        $ext = substr($file, strrpos($file, '.'));
        if ((strtolower($ext) == ".mp3") or (strtolower($ext) == ".ogg") or (strtolower($ext) == ".flac") or (strtolower($ext) == ".wma") or (strtolower($ext) == ".m4a") or (strtolower($ext) == ".mp4"))
        {
            $dir =  str_replace("'", "''", $root.$file);
            $file = str_replace("'", "''", $file);
            $query = $dbh->query("SELECT count(song_id) FROM music WHERE `song_album`='$alb_id' AND `song_art`='$art_id' AND `song_name`='$file'");
            $queryArr = $query->fetchAll();
            if ($queryArr[0][0] == "0")
            {
                $dbh->exec("INSERT INTO music(song_id, song_path, song_name, song_album, song_art) VALUES ('$sngMax', '$dir', '$file', '$alb_id', '$art_id')") or
					die("FATAL ERROR: Inserting: \"$dir\" into music with ID: $sngMax\n".implode(" ", $dbh->errorInfo()));
				$sngMax++;	
                $sngCount++;
				echo "<script language=\"javascript\">document.getElementById('sng').innerHTML = $sngCount;</script>";
				flush(); ob_flush();				
            }
        }
    }
    closedir($dirH);
}
?>
<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href="./index.php?p=main">Search/Browse</a></li>
			<li><a href="./index.php?p=pl">My Playlists</a></li>
			<li><a href="./index.php?p=adm">Aministration</a></li>
			<li><a href="./index.php?p=about">About</a></li>
			<li><a href="./logout.php">Logout</a></li>	
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv"></div>
</div>
<div id="main">
	<p style="font-weight: bold; ">Updating the Database</p>
	- Current DB read<br />
	- Scaning music directory(<?php echo $root; ?>) for new songs... DO NOT HIT THE BACK BUTTON ON YOUR BROWSER<br />
	&nbsp;&nbsp; - Artists: <span id='art'><?php echo $artCount; ?></span><br />
	&nbsp;&nbsp; - Albums: <span id='alb'><?php echo $albCount; ?></span><br />
	&nbsp;&nbsp; - Songs: <span id='sng'><?php echo $sngCount; ?></span><br />
	<?php
	flush(); ob_flush();
	
	//lets fill in the table:
	$dirH = opendir($root);
	if(!$dirH)
	  die("FATAL ERROR: Can't read directory: $root");
	while (($file = readdir($dirH)) !== false)
	{
	    if (($file == ".") or ($file == ".."))
	        continue;
	    if (is_dir($root.$file))
	    {
	        $dir = $root.$file."/";
	        $file = str_replace("'", "''", $file);
	        //echo "$artCount - $file<br/>";
	        $query = $dbh->query("SELECT art_id FROM artists WHERE `art_name`='$file'");
	        $queryArr = $query->fetchAll();
	        if ($queryArr[0][0] == "")
	        {
	            $dbh->exec("INSERT INTO artists(art_id, art_name) VALUES ('$artMax', '$file')") or        
	                die("FATAL ERROR: Inserting: $file into artists with ID: $artMax\n".implode(" ", $dbh->errorInfo()));
	            $art_id = $artMax;
				$artMax++;
			    $artCount++;
				echo "<script language=\"javascript\">document.getElementById('art').innerHTML = $artCount;</script>";
				flush(); ob_flush();
	        }
	        else
	        {
	            $art_id = $queryArr[0][0];
	        }
	        processArtDir($dir, $art_id);
	    }
	}
	closedir($dirH);
	$dbh = null;
	?>
	<br/><p style="font-size: 16px; color: red; ">DONE</p>
</div>