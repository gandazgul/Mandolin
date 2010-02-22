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

<div id="teaser">
	<div id="errorDiv" class="important"></div>
	<form action="" class="ui-form">
		<div>
			<label for="sQuery" class="title">Type in Artist, Album or Song name: </label>
			<input type="text" id="sQuery" onkeyup="search(this.value, true)" size="85" class="side ui-widget-content ui-corner-all" />
		</div>
	</form>
</div>
<div id="main">
	<div class="subcolumns">
		<div class="c25l">
			<div class="subcl2">
				<p class="title">Artists:&nbsp;<span id="artTotal"></span></p>
			</div>
		</div>
		<div class="c50l">
			<div class="subcl1">
				<p class="title">Albums for selected Artists:</p>
			</div>
		</div>
		<div class="c25l">
			<div class="subcl1">
				<p class="title">Songs for selected Albums:</p>
			</div>
		</div>
	</div>
	<div class="subcolumns">
		<div class="c25l">
			<div class="subcl2 musicList" id="artistsListDiv">
				<ol id="artistsList">
					<li><img alt="Loading..." src="./client/images/ajax-loader.gif" /></li>
				</ol>
			</div>
		</div>
		<div class="c50l">
			<div class="subcl1 musicList" id="albumListDiv">
				<ol id="albumList"></ol>
			</div>
		</div>
		<div class="c25l">
			<div class="subcl1 musicList" id="songListDiv">
				<ol id="songList"></ol>
			</div>
		</div>
	</div>
</div>