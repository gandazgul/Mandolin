<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=music");
		exit();
	}
?>
<link type="text/css" rel="stylesheet" href="./client/css/lib/jquery.contextMenu.css" />
<script type="text/javascript" src="./client/js/lib/jquery.tablesorter.min.js"></script>

<form method="post" action="./server/music.php" id="playForm">
	<div>
		<input type="hidden" name="a" value="play">
		<input type="hidden" name="sng" id="sng">
		<input type="hidden" name="SID" id="SID">
		<input type="hidden" name="rnd" id="rnd" value="false">
	</div>
</form>

<div id="addToPLDiag" title="Add selected songs to a playlist">
	<form action="" class="ui-form ui-widget">
		<div>
			<label for="tmpPlList">Select a playlist:</label>
			<select id="tmpPlList" class="ui-widget-content ui-corner-all text"></select>
		</div>
	</form>
</div>

<ul id="artnAlbMenu" class="contextMenu">
	<li class="play"><a href="#play">Play</a></li>
	<li class="playrand"><a href="#playrand">Play Random</a></li>	
	<!--li class="rename separator"><a href="#rename">Rename</a></li>
	<li class="delete"><a href="#delete">Delete</a></li-->
	<li class="cancel separator"><a href="#cancel">Cancel</a></li>
</ul>

<ul id="songsMenu" class="contextMenu">
	<li class="play"><a href="#play">Play</a></li>
	<li class="playrand"><a href="#playrand">Play Random</a></li>
	<li class="selectall separator"><a href="#selectall">Select All</a></li>
	<li class="createpl separator"><a href="#createpl">Create Playlist</a></li>
	<li class="addtopl"><a href="#addtopl">Add to Existing Playlist</a></li>
	<!--li class="rename separator"><a href="#rename">Rename</a></li>
	<li class="delete"><a href="#delete">Delete</a></li-->
	<li class="cancel separator"><a href="#cancel">Cancel</a></li>
</ul>

<div id="errorDiv"></div>

<div id="main">
	<div class="songPager"></div>
	<table id="songList" class="tablesorter">
		<thead>
			<tr>
				<th>Title</th><th>Album</th><th>Artist</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
	<div class="songPager"></div>
</div>