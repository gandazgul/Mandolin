<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=about");
		exit();
	}
?>
<div id="main" class="ui-widget-content ui-corner-all">
	<h3>What is Mandolin?</h3>
	<p><strong>Mandolin</strong> is a web application that makes your music library searchable,
	and enables you to stream any song on demand from any computer with an internet connection. It also lets you make Playlists and save them to 
	stream them later at any time.</p>
	<h3>Meaning of the name</h3>
	<p>man·do·lin /ˈmændlɪn, ˌmændlˈɪn/ Spelled[man-dl-in, man-dl-in]</p>
	<dl>
		<dt>–noun</dt>
		<dd>
			<p>A small lutelike instrument with a typically pear-shaped body and a straight fretted neck, having usually four sets of paired strings
				tuned in unison or octaves.<p>
			<h4>Origin:</h4>
			<p>[French mandoline, from Italian mandolino, diminutive of mandola, lute, from French mandore, from Late Latin pandūra, three-string
				lute, from Greek pandoura.]</p>
		</dd>
	</dl>
	<p>I chose it because I wanted the logo and icon to be a guitar, but guitars imagery and the word 'guitar' and its variations are so common and
		used so much that I couldnt find anything that I could use. My girlfriend Amanda, likes to be called Mandolin and that gave me the idea,
		the Mandolin is after all a guitar-like instrument, and I like the name.</p>
	<p>Before beign called Mandolin this project was called:</p>
		<ul>
			<li>The music thing :)</li>
			<li>Music Server</li>
			<li>Music Streaming Server or MSTServer - (this one sounds a lot like its made by Microsoft :P)</li>
		</ul>
	<h3>Browser Compatibility</h3>
	<p>We try to make <strong>Mandolin</strong> cross-browser compatible:<br />
	<ul>
		<h4>Tested and works fully in:</h4>
		<li>FF3.x, Chrome 3 and 4, Safari for Windows 3.2.2 and 4.0, IE8, Opera 10.5</li>
		<br/>
		<h4>Works, but it has some visual problems, not affecting function:</h4>
		<li>IE6 and IE7</li>
		<br/>
		<h4>Will test in:</h4>
		<li>FF for Mac, Safari for Mac</li>
		<br/>
	</ul>
	<h3>Where to get the source code</h3>
	<p>
		<strong>Mandolin</strong> is hosted @Github. The lastest code is on the branch: <strong>master</strong>. Each stable release is marked with a tag named after
		the version, you can download packed tags from Github:
	</p>
	<p><a href="http://github.com/gandazgul/Mandolin">Mandolin at GitHub</a></p>
	<p>
		Other branches include older versions (mandolinX where X is the major version number) that are kept to support them a little longer or for
		historical and sentimental issues :)<br/>
		Any other branch are just experimental or test branches and are usually broken.
	</p>
	<h3>GUI Design</h3>
	<p>The design, template and much of the CSS is based on <a href="http://www.yaml.de/" target="_blank">YAML</a>. Specially on their dummy project and code generated by the YAML builder.
	<h3>The Icons</h3>
	<p>From the Silk Icon Set made by Mark James and can be downloaded from <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">FamFamFam.com</a></p>
	<p>Also from the Silk Icon Companion Set by Damien Guard and can be downloaded from <a href="http://damieng.com/creative/icons/silk-companion-1-icons" target="_blank">damieng.com</a>
</div>
