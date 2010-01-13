<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=pl");
		exit();
	}
?>
<form method="post" action="./server/music.php" id="downForm">
	<input type="hidden" name="a" value="play" />
	<input type="hidden" name="pl" id="pl" />
	<input type="hidden" name="SID" id="SID" />
	<input type="hidden" name="rnd" id="rnd" value="false" />
</form>
<div id="main">
	<div class="subcolumns">
		<div class="c33l">
			<p class="title">Saved Playlists:&nbsp;<span id="plTotal"></span></p>
			<div class="subcl musicList p20">
				<ol id="plList">
					<li><img alt="Loading..." src="./client/images/ajax-loader.gif" /></li>
				</ol>
			</div>
		</div>
		<div class="c33l">
			<div class="subcl">
				<p class="title">List content:&nbsp;<span id="listName"></span></p>
				<div class="musicList">
					<ol id="plContents"></ol>
				</div>
			</div>
		</div>
		<div class="c33l">
			<div class="subcl" style="border-left: 1px dotted #DDDDDD; padding-left: 10px; padding-right: 20px; ">
				<h6 class="vlist">Current Playlist Selection</h6>
				<ul class="vlist">
					<li><a href="javascript:playPL()">Play Selected</a></li>
					<li><a href="javascript:ranPlayPL()">Shuffle then Play</a></li>
					<li><a href="javascript:renPL()">Rename Playlist</a></li>
					<li><a href="javascript:delPL()">Delete Playlist</a></li>
				</ul>
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