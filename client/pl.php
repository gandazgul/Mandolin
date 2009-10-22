<?php
	if (!isset($sess_id))
	{
		header("Location: ./?p=logout");
		exit();
	}
?>
<script type="text/javascript" language="javascript" src="./client/js/pl.js"></script>
<form method="post" action="./server/ls.php" id="downForm">
	<input type="hidden" name="a" value="play" />
	<input type="hidden" name="pl" id="pl" />
	<input type="hidden" name="rnd" id="rnd" value="false" />
</form>
<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href=".">Music</a></li>
			<li class="active"><strong>Music Playlists</strong></li>
			<li><a href="./?p=movies">Movies</a></li>
			<li><a href="./?p=adm">Aministration</a></li>
			<li><a href="./?p=about">About</a></li>
			<li><a href="./?p=logout">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv" class="important" style="display: none"></div>
</div>
<div id="main">
	<div class="subcolumns">
	  <div class="c33l">
	    <div class="subcl" style="padding-left: 20px; ">
			<p class="title">Saved Playlists:&nbsp;<span id="plTotal"></span></p>
			<select id="plList" size="20" style="width: 100%; " onchange="plOnChange()"></select>		
	    </div>
	  </div>
	  <div class="c33l">
	    <div class="subcl">
			<p class="title">List content:&nbsp;<span id="listName"></span></p>
			<select id="plContents" size="20" style="width: 100%; " multiple="multiple"></select>			 
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