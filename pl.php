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
<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href="./index.php">Search/Browse</a></li>
			<li class="active"><strong>My Playlists</strong></li>
			<li><a href="./index.php?p=adm">Aministration</a></li>
			<li><a href="./index.php?p=about">About</a></li>
			<li><a href="./logout.php">Logout</a></li>			
		</ul>
	</div>
</div>
<div id="main">
	<div class="subcolumns">
	  <div class="c33l">
	    <div class="subcl">
			<p class="title">Saved Playlists:&nbsp;<span id="plTotal"></span></p>
			<select id="plList" size="20" style="width: 240px; " onchange="_plOnChange(this)" multiple="multiple"></select>		
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
			  <li>Play Selected</li>
  			  <li>Play Selected Randomly</li>
  			  <li>Play Each Randomly</li>
			  <li>Delete Playlist</li>
			  <li>Shuffle Playlist</li>			  
			</ul>
			<h6 class="vlist">Current Song Selection</h6>
			<ul class="vlist">
			  <li>Delete from playlist</li>
  			  <li>Create a new playlist</li>
			  <li>Move up</li>
			  <li>Move down</li>
			</ul>			
	    </div>
	  </div>
	</div>
</div>