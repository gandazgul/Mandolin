<?php
	if (!isset($sess_id))
	{
		header("Location: ./index.php");
		exit();
	}
?>
<script type="text/javascript">
	<?php include_once("./js/pl.js"); ?>
</script>
<form method="post" action="./ls.php" id="downForm">
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
			<li><a href="./index.php?p=main">Search/Browse</a></li>
			<li class="active"><strong>My Playlists</strong></li>
			<li><a href="./index.php?p=adm">Aministration</a></li>
			<li><a href="./index.php?p=about">About</a></li>
			<li><a href="./logout.php">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv"></div>
</div>
<div id="main">
	<div class="subcolumns">
	  <div class="c33l">
	    <div class="subcl">
			<p class="title">Saved Playlists:&nbsp;<span id="plTotal"></span></p>
			<select id="plList" size="20" style="width: 240px; " onchange="_plOnChange(this)"></select>		
	    </div>
	  </div>
	  <div class="c33l">
	    <div class="subl">
			<p class="title">List content:&nbsp;<span id="listName"></span></p>
			<select id="plContents" size="20" style="width: 240px; " multiple="multiple"></select>			 
	    </div>
	  </div>
	  <div class="c33l">
	    <div class="subcl">
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