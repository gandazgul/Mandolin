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
			<li><a href="./?p=logout">Logout</a></li>							
		</ul>
	</div>
</div>
<div id="main" style="padding-left: 20px">
	<p><span class="title">Mandolin</span> is a web application that makes your music library searchable, 
	and enables you to stream any song on demand from any computer with an internet connection. It also lets you make Playlists and save them to 
	stream them later at any time.</p>
	<p>We try to make <span class="title">Mandolin</span> cross-browser compatible:<br />
	<ul>
		<li class="ulTitle">Tested and works fully in:</li>
		<li>FF3.x, Chrome 2 and 3, Safari for Windows 3.2.2, IE8</li>
		<li class="ulTitle">Works, but it has some visual problems, not affecting function:</li>
		<li>IE6 and IE7</li> 
		<li class="ulTitle">Will test in:</li>
		<li>FF2.x, FF for Mac, Safari 4.x for PC and Mac</li> 
		<li class="ulTitle">Backburner:</li>
		<li>Opera, Netscape, other Browsers</li>
	</ul> 
	<p class="title">You can download the lastest code(branch: master) as well as the last stable release(branch: stable) of Mandolin from here(git):<br /> 
	<a href="git://github.com/gandazgul/Mandolin.git">Mandolin at GitHub</a><br />
	Any other branch are just experimental or test branches and are usually broken.
	</p>
	
</div>
