<?php
	if (!isset($sess_id))
	{
		header("Location: .");
		exit();
	}
?>
<script type="text/javascript" src="./js/lib/jquery.contextMenu.js"></script>
<link type="text/css" rel="stylesheet" href="./css/jquery.contextMenu.css" />
<script type="text/javascript">
	<?php include_once("./js/movies.js"); ?>
</script>
<style type="text/css">
	#feedback { font-size: 1.4em; }
	#moviesList .ui-selecting { background: #EDF2F8; }
	#moviesList .ui-selected { background: #C8DDF3; }
	#moviesList { list-style-type: none; margin: 0; padding: 0; }
	#moviesList li { list-style-type: none; margin: 1px 0; padding: 0.3em; font-size: 1.4em; }
</style>

<ul id="moviesMenu" class="contextMenu">
	<li class="ccat"><a href="#changecat">Change Category</a></li>
	<li class="rename"><a href="#rename">Rename</a></li>
	<li class="delete"><a href="#delete">Delete</a></li>
	<li class="cancel separator"><a href="#cancel">Cancel</a></li>
</ul>

<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href=".">Music</a></li>
			<li><a href="./?p=pl">Music Playlists</a></li>
			<li class="active"><strong>Movies</strong></li>
			<li><a href="./?p=adm">Aministration</a></li>
			<li><a href="./?p=about">About</a></li>
			<li><a href="./logout.php">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv"></div>
	<p>
		<label for="sQuery" class="title">Type in a movie title: </label>
		<input type="text" id="sQuery" onkeyup="search(this.value, true)" size="85" />
	</p>
</div>
<div id="main">
	<div class="subcolumns">
		<div class="c33l">
			<div class="subcl" id="moviesListDiv" style="padding-left: 20px; height: 350px; overflow-y: auto; overflow-x: hidden; padding: 0;">
				<ol id="moviesList"></ol>
			</div>
		</div>
		<div class="c66l">
			<div class="subcl" style="height: 350px; overflow-y: auto; overflow-x: hidden; padding: 0 1em;">
				<div id="movieContainer"></div>
			</div>
		</div>
	</div>
</div>