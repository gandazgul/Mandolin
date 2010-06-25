<div id="nav">
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
		?>
		</ul>
	</div>
</div>
