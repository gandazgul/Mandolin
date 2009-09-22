<?php
if (!isset($sess_id) or ($_SESSION['userAdminLevel'] != 0))
{
	header("Location: ./index.php");
	exit();
}

require_once './models/MusicDB.php';
ini_set('max_execution_time', '6000');

$musicDB = new MusicDB("./db/music.db");

$musicDB->recreateDB();
//---------------------------------------------NOW LET'S FILL THE DATABASE----------------------------------------
?>
<div id='nav'>
	<!-- skiplink anchor: navigation -->
	<a id='navigation' name='navigation'></a>
	<div class='hlist'>
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href='./?p=music'>Music</a></li>
			<li><a href='./?p=pl'>Music Playlists</a></li>
			<li><a href='./?p=movies'>Movies</a></li>
			<li><a href='./?p=adm'>Aministration</a></li>
			<li><a href='./?p=about'>About</a></li>
			<li><a href='./logout.php'>Logout</a></li>
		</ul>
	</div>
</div>
<div id='teaser'>
	<div id='errorDiv'></div>
</div>
<div id='main'>
	<h1>Creating the Database</h1>
	<ul>
		<li>Database deleted and new one created</li>
		<li>Scanning directories to add music to the new DB - <span style='color: #CC3300; '>DO NOT HIT THE BACK BUTTON ON YOUR BROWSER!!!</span></li>
		<ul>
			<li>Artists: <span id='art'></span></li>
			<li>Albums:  <span id='alb'></span></li>
			<li>Songs: <span id='sng'></span></li>
		</ul>
	</ul>
</div>
<?php $musicDB->addToDB('C:/drivers/', strlen('C:/drivers/')); ?>
