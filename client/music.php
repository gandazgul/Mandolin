<?php
	if (!isset($sess_id))
	{
		header("Location: .");
		exit();
	}
?>
<link type="text/css" rel="stylesheet" href="./client/css/jquery.contextMenu.css" />
<style type="text/css">
	#feedback { font-size: 1.4em; }
	#artistsList .ui-selecting, #albumList .ui-selecting, #songList .ui-selecting { background: #EDF2F8; }
	#artistsList .ui-selected, #songList .ui-selected { background: #C8DDF3; }
	#albumList .ui-selected { border: 1px solid orange; }
	#artistsList, #albumList, #songList { list-style-type: none; margin: 0; padding: 0; }
	#artistsList li { list-style-type: none; margin: 1px 0; padding: 0.3em; font-size: 11px; font-weight: bold; }
	#albumList li { list-style-type: none; margin: 1px 2px 3px; padding: 1px; float: left; width: 110px; height: 90px; font-size: 11px; font-weight: bold; text-align: center;  }	
	#songList li { list-style-type: none; margin: 1px 0; padding: 0.3em; font-size: 11px; font-weight: bold; }
</style>

<script type="text/javascript" language="javascript" src="./client/js/lib/jquery.contextMenu.js"></script>
<script type="text/javascript" language="javascript" src="./client/js/music.js"></script>

<form method="post" action="./server/ls.php" id="playForm">
	<input type="hidden" name="a" value="play" />
	<input type="hidden" name="sng" id="sng" />
	<input type="hidden" name="rnd" id="rnd" value="false" />
</form>
<div id="dialog" title="Add selected songs to a playlist">
	<form>
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

<form class="yform" style="display: none">
	<fieldset>
		<!--legend></legend-->
		<div class="type-text">
			<input type="hidden" id="sngID" />
			<label for="sngComm">This is a note left by another user for the selected song, you can change it here</label>
			<br />
			<input type="text" id="sngComm" style="width: auto;" />
		</div>
		<div class="type-button">
			<input type="button" onclick="setComm()" value="Save new note" />
		</div>
	</fieldset>
</form>

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
			<li><a href="./?p=logout">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv" class="important" style="display: none"></div>
	<p>
		<label for="sQuery" class="title">Type in Artist, Album or Song name: </label>
		<input type="text" id="sQuery" onkeyup="search(this.value, true)" size="85" />
	</p>
</div>
<div id="main">
	<div class="subcolumns">
		<div class="c25l">
			<div class="subcl" id="artistsListDiv" style="height: 350px; overflow-y: auto; overflow-x: hidden; padding: 0 0 0 20px;">
				<ol id="artistsList"></ol>
			</div>
		</div>
		<div class="c50l" style="width: 49.9%; "><!-- IE7 Hack -->
			<div class="subcl" id="albumListDiv" style="height: 350px; overflow-y: auto; overflow-x: hidden; padding: 0 0 0 10px;">
				<ol id="albumList"></ol>
			</div>
		</div>
		<div class="c25l">
			<div class="subcl" id="songListDiv" style="height: 350px; overflow-y: auto; overflow-x: hidden; padding: 0 0 0 10px;"">
				<ol id="songList"></ol>
			</div>
		</div>
	</div>
</div>