<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<ul>
		<?php
		if ($p == "music")
			echo "<li class='active'><strong>Music</strong></li>";
		else
			echo "<li><a href='.'>Music</a></li>";

		if ($p == "playlists")
			echo "<li class='active'><strong>Music Playlists</strong></li>";
		else
			echo "<li><a href='./?p=playlists'>Music Playlists</a></li>";

		if ($p == "movies")
			echo "<li class='active'><strong>Movies</strong></li>";
		else
			echo "<li><a href='./?p=movies'>Movies</a></li>";

		if ($p == "adm")
			echo "<li class='active'><strong>Administration</strong></li>";
		else
			echo "<li><a href='./?p=adm'>Administration</a></li>";

		if ($p == "about")
			echo "<li class='active'><strong>About</strong></li>";
		else
			echo "<li><a href='./?p=about'>About</a></li>";


		echo "<li><a href='./?p=logout'>Logout</a></li>";
		?>
		</ul>
	</div>
</div>
