<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=music");
		exit();
	}
?>
<link type="text/css" rel="stylesheet" href="./client/css/lib/jquery.contextMenu.css" />
<script type="text/javascript" src="./client/js/lib/jquery.contextMenu.js"></script>

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

<div id="errorDiv" class="important"></div>
<div id="teaser" class="ui-widget-content ui-corner-all">
	<form action="" class="ui-form">
		<label for="sQuery" class="title">To begin searching type in Artist, Album or Song name:&nbsp;&nbsp;</label>
		<input type="text" id="searchBox" size="110" class="text-side-no-margin ui-widget-content ui-corner-all" />
	</form>
</div>
<div id="main" class="ui-widget-content ui-corner-all">
	<div class="subcolumns">
		<div class="c25l">
			<div>
				<h3><img alt="Artists Icon" src="./client/images/artists.png" />Artists:&nbsp;<span id="artTotal"></span></h3>
			</div>
			<div class="musicList" id="artistsListDiv">
				<ol id="artistsList">
					<li><img alt="Loading..." src="./client/images/ajax-loader.gif" /></li>
				</ol>
			</div>
		</div>
		<div class="c50l">
			<div class="subcl1">
				<h3><img alt="Albums Icon" src="./client/images/cd.png" />&nbsp;Albums for selected Artist(s):</h3>
			</div>
			<div class="subcl1 musicList" id="albumListDiv">
				<ol id="albumList"></ol>
			</div>
		</div>
		<div class="c25l">
			<div class="subcl1">
				<h3><img alt="Songs Icon" src="./client/images/song.png" />&nbsp;Songs for selected Album(s):</h3>
			</div>
			<div class="subcl1 musicList" id="songListDiv">
				<ol id="songList"></ol>
			</div>
		</div>
	</div>
</div>