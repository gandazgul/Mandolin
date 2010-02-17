<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=about");
		exit();
	}
?>
<div id="main" style="padding-left: 20px">
	<p><span class="title">Mandolin</span> is a web application that makes your music library searchable, 
	and enables you to stream any song on demand from any computer with an internet connection. It also lets you make Playlists and save them to 
	stream them later at any time.</p>
	<p>We try to make <span class="title">Mandolin</span> cross-browser compatible:<br />
	<ul>
		<li class="ulTitle">Tested and works fully in:</li>
		<li>FF3.x, Chrome 3 and 4, Safari for Windows 3.2.2 and 4.0, IE8</li>
		<li class="ulTitle">Works, but it has some visual problems, not affecting function:</li>
		<li>IE6 and IE7</li>
		<li class="ulTitle">Will test in:</li>
		<li>FF2.x, FF for Mac, Safari for Mac</li>
		<li class="ulTitle">Backburner:</li>
		<li>Opera, Netscape, other Browsers</li>
	</ul> 
	<p class="title">Mandolin is hosted @Github. The lastest code is on the branch: master. The last stable release is marked with a tag named after the version, you can download packed tags from Github:<br />
	<a href="http://github.com/gandazgul/Mandolin">Mandolin at GitHub</a><br />
	Other branches include older versions (mandolinX where X is the major version number) that are kept to support them a little longer or for historical and sentimental issues :)
	Any other branch are just experimental or test branches and are usually broken.
	</p>
	
</div>
