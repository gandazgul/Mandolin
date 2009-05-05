<?php
	if (!isset($sess_id))
		header("Location: ./index.php");
?>



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
		</ul>
	</div>
</div>
<div id="teaser">
	<p>
		<label for="sQuery"><p class="title">Type in Artist, Album or Song name: 
		<input type="text" id="sQuery" onkeyup="search(this.value, true)" size="82" /></p></label>
	</p>
</div>
<div id="main">
	<div id="col1">
	  <div id="col1_content" class="clearfix">
	    <!-- add your content here -->
	    <div class="subcolumns">
	      <div class="c33l">
	        <div class="subcl">
				<p class="title">Artists</p>
				<select id="artList" size="20" style="width: 240px; " onchange="_artOnChange(this)" multiple="multiple"></select>			 
	        </div>
	      </div>
	      <div class="c33l">
	        <div class="subc">
				<p class="title">Albums</p>
				<select id="albList" size="20" style="width: 240px; " onchange="_albOnChange(this)" multiple="multiple"></select>
	        </div>
	      </div>
	      <div class="c33r">
	        <div class="subcr">
				<p class="title">Songs</p>
				<select id="songList" size="20" style="width: 240px; " onchange="sngOnChange(this.value)" multiple="multiple"></select>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<div id="col3">
	  <div id="col3_content" class="clearfix">
		<p>This is a note left by another user for the selected song, you can change it here</p>
		<span id="sngID" style="display: none;"></span>
		<input type="text" id="sngComm" size="36" />&nbsp;<input type="button" onclick="setComm()" value="Save new note" />
		<br /><br />
		<h6 class="vlist">H6 Heading</h6>
		<ul class="vlist">
		  <li>List Item 1</li>
		  <li>List Item 2</li>
		  <li>List Item 3</li>
		  <li>List Item 4</li>
		  <li>List Item 5</li>
		</ul>
	  </div>
	  <!-- IE Column Clearing -->
	  <div id="ie_clearing"> &#160; </div>
	</div>
</div>