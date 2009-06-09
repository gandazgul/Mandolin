<?php
	if (!isset($sess_id))
	{
		header("Location: ./index.php");
		exit();
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
			<li class="active"><strong>About</strong></li>	
			<li><a href="./logout.php">Logout</a></li>					
		</ul>
	</div>
</div>
<div id="main" style="padding-left: 20px">
	<p>newMusicServer is a rewrite of mstServer 0.5, I decided to start anew with all the experiences 
	I acumulated developing mstServer I am writing this one much cleaner.</p>
	<p class="title">You can download the last version of mstServer from here:</p><a href="https://launchpad.net/mstserver">https://launchpad.net/mstserver</a><br /><br />
	Not sure where to publish this code now, since I am using git for version control, I wish launchpad used git instead of bazaar. Maybe GitHub will do.
</div>