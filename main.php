<?php
	if (!isset($sess_id))
	{
		header("Location: ./index.php");
		exit();
	}
?>
<script type="text/javascript">
	<?php include_once("./js/main.js"); ?>
</script>
<style type="text/css">
	#feedback { font-size: 1.4em; }
	#artistsList .ui-selecting { background: #FECA40; }
	#artistsList .ui-selected { background: #F39814; color: white; }
	#artistsList { list-style-type: none; margin: 0; padding: 0; width: 60%; }
	#artistsList li { margin: 3px; padding: 0.4em; font-size: 1.4em; height: 18px; }
</style>
<form method="post" action="./ls.php" id="playForm">
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

<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li class="active"><strong>Search/Browse</strong></li>
			<li><a href="./index.php?p=pl">My Playlists</a></li>
			<li><a href="./index.php?p=adm">Aministration</a></li>
			<li><a href="./index.php?p=about">About</a></li>
			<li><a href="./logout.php">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv"></div>
	<p>
		<label for="sQuery" class="title">Type in Artist, Album or Song name: </label>
		<input type="text" id="sQuery" onkeyup="search(this.value, true)" size="85" />
	</p>
</div>
<div id="main">
	<div id="col1">
	  <div id="col1_content" class="clearfix">
		<ol id="artistsList">
			<li class="ui-widget-content">Item 1</li>
			<li class="ui-widget-content">Item 2</li>
			<li class="ui-widget-content">Item 3</li>
			<li class="ui-widget-content">Item 4</li>
			<li class="ui-widget-content">Item 5</li>
			<li class="ui-widget-content">Item 6</li>
			<li class="ui-widget-content">Item 7</li>
		</ol>
	  </div>
	</div>
	<div id="col3">
	  <div id="col3_content" class="clearfix">
		<h6 class="vlist">Current Song Selection</h6>
		<ul class="vlist">
		  <li><a href="javascript:selPlay()">Play Selected</a></li>
		  <li><a href="javascript:selRandPlay()">Play Selected Randomly</a></li>
		  <li><a href="javascript:createPlaylist()">Create a new playlist</a></li>
		  <li><a href="javascript:_addToPlaylist()">Add to a playlist</a></li>
		</ul>
		<form class="yform">
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
	  </div>
	  <!-- IE Column Clearing -->
	  <div id="ie_clearing"> &#160; </div>
	</div>
</div>