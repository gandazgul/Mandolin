<?php
	if (!isset($sess_id))
	{
		header("Location: .");
		exit();
	}
?>
<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href=".">Music</a></li>
			<li><a href="./?p=pl">Music Playlists</a></li>
			<li><a href="./?p=movies">Movies</a></li>
			<li><a href="./?p=adm">Aministration</a></li>
			<li class="active"><strong>About</strong></li>
			<li><a href="./logout.php">Logout</a></li>							
		</ul>
	</div>
</div>
<div id="main" style="padding-left: 20px">
	<p><span class="title">newMusicServer</span> for a lack of a better name is a web application that makes your music library searchable, 
	and enables you to stream any song on demand from any computer with an internet connection. It also lets you make Playlists and save them to 
	stream them later at any time.</p>
	<p><span class="title">newMusicServer</span> is cross-browser compatible:<br />
	<ul>
		<li class="ulTitle">Tested in:</li>
		<li>FF2.x, FF3.x, IE7, Google Chrome, Safari 3.2.2</li>
		<li class="ulTitle">Will test in:</li>
		<li>IE8, FF Mac, Safari 4.x PC and Mac versions.</li> 
		<li class="ulTitle">Backburner:</li>
		<li>IE6</li>
	</ul> 
	<p class="title">You can download the lastest code(branch: master) as well as the last stable release(branch: stable) of newMusicServer from here(git):<br /> 
	<a href="git://github.com/gandazgul/newmusicserver.git">newMusicServer at GitHub</a><br />
	Any other branch are just experimental or test branches and are ussually broken.
	</p>
	
</div>
