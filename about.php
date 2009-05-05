<?php
	if (!isset($sess_id))
		header("Location: ./index.php");
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
		</ul>
	</div>
	<div class="title" style="width: 100%; text-align: right; width: 290px; position: absolute; right: 10px; top: 170px; ">
		Artists: <span id="artTotal"></span>,&nbsp;
		Albums:  <span id="albTotal"></span>,&nbsp;
		Songs:  <span id="sngTotal"></span>
	</div>	
</div>
<div id="main">
	<p>newMusicServer is a rewrite of mstServer 0.5, I decided to start anew with all the experiences 
	I acumulated developing mstServer I am writing this one much cleaner.</p>
	<p class="title">You can download the last version of mstServer from here:</p><a href="https://launchpad.net/mstserver">https://launchpad.net/mstserver</a><br /><br />
	Not sure where to publish this code now, since I am using git for version control, I wish launchpad used git instead of bazaar. Maybe GitHub will do.
</div>