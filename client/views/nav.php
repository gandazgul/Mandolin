<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<ul>
		<?php
		if ($p == "music")
			echo "<li class='active'><strong><img src='./client/images/music.png' class='nav_icon' />Music</strong></li>";
		else
			echo "<li><a href='./?p=music'><img src='./client/images/music.png' class='nav_icon' />Music</a></li>";

		if ($p == "playlists")
			echo "<li class='active'><strong><img src='./client/images/playlists.png' class='nav_icon' />Playlists</strong></li>";
		else
			echo "<li><a href='./?p=playlists'><img src='./client/images/playlists.png' class='nav_icon' />Playlists</a></li>";

		/*if ($p == "movies")
			echo "<li class='active'><strong>Movies</strong></li>";
		else
			echo "<li><a href='./?p=movies'>Movies</a></li>";*/

		if ($p == "adm")
			echo "<li class='active'><strong><img src='./client/images/cog.png' class='nav_icon' />Administration</strong></li>";
		else
			echo "<li><a href='./?p=adm'><img src='./client/images/cog.png' class='nav_icon' />Administration</a></li>";

		if ($p == "about")
			echo "<li class='active'><strong><img src='./client/images/information.png' class='nav_icon' />About</strong></li>";
		else
			echo "<li><a href='./?p=about'><img src='./client/images/information.png' class='nav_icon' />About</a></li>";


		echo "<li><a href='./?p=logout'><img src='./client/images/logout.png' class='nav_icon' />Logout</a></li>";
		?>
		</ul>
	</div>
</div>
