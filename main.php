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
<div id="main">
	<p>Enter a search term, it will be matched against artist, album and song in that order</p>
	<label for="sQuery"><p class="title">Type in Artist, Album or Song name: <input type="text" id="sQuery" onkeyup="search(this)"></label>
	<div class="subcolumns">
		<div class="c33l">
			<div class="subcl">
				<!-- Insert your subtemplate content here -->
				<p class="title">Artists</p>
				<select id="artList" size="20" style="width: 300px; "></select>
			</div>
		</div>
		<div class="c33l">
			<div class="subc">
				<!-- Insert your subtemplate content here -->
				<p class="title">Albums</p>
				<select id="albList" size="20" style="width: 300px; "></select>
			</div>
		</div>
		<div class="c33r">
			<div class="subcr">
				<!-- Insert your subtemplate content here -->
				<p class="title">Songs</p>
				<select id="songList" size="20" style="width: 300px; "></select>
			</div>
		</div>
	</div>
	<div>
		<label for="sngComm"><p class="title">Comments:</p><input type="text" id="sngComm" size="50" /></label>
	</div>
</div>