<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=music");
		exit();
	}
?>
<link type="text/css" rel="stylesheet" href="./client/css/jquery.contextMenu.css" />
<script type="text/javascript" language="javascript" src="./client/js/lib/jquery.contextMenu.js"></script>

<form method="post" action="./server/music.php" id="playForm">
	<input type="hidden" name="a" value="play" />
	<input type="hidden" name="sng" id="sng" />
	<input type="hidden" name="SID" id="SID" />
	<input type="hidden" name="rnd" id="rnd" value="false" />
</form>
<div id="dialog" title="Add selected songs to a playlist">
	<form action="">
	<fieldset>
		<label for="tmpPlList">Select a playlist:</label>
		<select id="tmpPlList"></select>
	</fieldset>
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

<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li class="active"><strong>Music</strong></li>
			<li><a href="./?p=pl">Music Playlists</a></li>
			<li><a href="./?p=movies">Movies</a></li>
			<li><a href="./?p=adm">Aministration</a></li>
			<li><a href="./?p=about">About</a></li>
			<li><a href="./client/logout.php">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv" class="important"></div>
	<p>
		<label for="sQuery" class="title">Type in Artist, Album or Song name: </label>
		<input type="text" id="sQuery" onkeyup="search(this.value, true)" size="85" />
	</p>
</div>
<div id="main">
	<div class="subcolumns">
		<div class="c25l">
			<div class="subcl musicList p20" id="artistsListDiv">
				<ol id="artistsList"></ol>
			</div>
		</div>
		<div class="c50l">
			<div class="subcl musicList" id="albumListDiv">
				<ol id="albumList"></ol>
			</div>
		</div>
		<div class="c25l">
			<div class="subcl musicList" id="songListDiv">
				<ol id="songList"></ol>
			</div>
		</div>
	</div>
</div>