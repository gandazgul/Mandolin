<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=pl");
		exit();
	}
?>

<link type="text/css" rel="stylesheet" href="./client/css/jquery.contextMenu.css" />
<script type="text/javascript" language="javascript" src="./client/js/lib/jquery.contextMenu.js"></script>

<ul id="plMenu" class="contextMenu">
	<li class="play"><a href="#play">Play Selected</a></li>
	<li class="playrand"><a href="#playrand">Shuffle then Play</a></li>
	<li class="shuffle separator"><a href="#shuffle">Shuffle Playlist</a></li>
	<li class="rename"><a href="#rename">Rename Playlist</a></li>
	<li class="delete"><a href="#delete">Delete Playlist</a></li>
	<li class="cancel separator"><a href="#cancel">Cancel</a></li>
</ul>

<ul id="songsMenu" class="contextMenu">
	<li class="play"><a href="#play">Play Selected</a></li>
	<li class="playrand"><a href="#playrand">Play Random</a></li>
	<li class="delete separator"><a href="#delete">Delete from Playlist</a></li>
	<li class="up separator"><a href="#moveup">Move up</a></li>
	<li class="down"><a href="#movedown">Move down</a></li>
	<li class="cancel separator"><a href="#cancel">Cancel</a></li>
</ul>

<form method="post" action="./server/music.php" id="downForm">
	<input type="hidden" name="a" value="play" />
	<input type="hidden" id="pl_or_sng" />
	<input type="hidden" name="SID" id="SID" />
	<input type="hidden" name="rnd" id="rnd" value="false" />
</form>
<div id="teaser">
	<div id="errorDiv" class="important"></div>
</div>
<div id="main">
	<div class="subcolumns">
		<div class="c33l">
			<div class="subcl2">
				<p class="title">Saved Playlists:&nbsp;<span id="plTotal"></span></p>
			</div>
		</div>
		<div class="c33l">
			<div class="subcl1">
				<p class="title">Contents for the selected Playlists:</p>
			</div>
		</div>
		<div class="c33l">
			
		</div>
	</div>
	<div class="subcolumns">
		<div class="c33l">
			<div class="subcl2 musicList">
				<ol id="plList">
					<li><img alt="Loading..." src="./client/images/ajax-loader.gif" /></li>
				</ol>
			</div>
		</div>
		<div class="c33l">
			<div class="subcl1 musicList">
				<ol id="plContents"></ol>
			</div>
		</div>
		<div class="c33l">
			<div class="subcl1" style="border-left: 1px dotted #DDDDDD; padding-left: 10px; padding-right: 20px; ">
				<h6 class="vlist">Current Song Selection</h6>
				<ul class="vlist">
					<li><a href="javascript:shuffle()">Shuffle Playlist</a></li>
					<li><a href="javascript:delFromPl()">Delete from playlist</a></li>
					<li><a href="javascript:move(true)">Move up</a></li>
					<li><a href="javascript:move(false)">Move down</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>