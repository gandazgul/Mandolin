<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=movies");
		exit();
	}
?>
<link type="text/css" rel="stylesheet" href="./client/css/lib/jquery.contextMenu.css" />
<script type="text/javascript" src="./client/js/lib/jquery.contextMenu.js"></script>

<ul id="moviesMenu" class="contextMenu">
	<li class="ccat"><a href="#changecat">Change Category</a></li>
	<li class="rename"><a href="#rename">Rename</a></li>
	<li class="delete"><a href="#delete">Delete</a></li>
	<li class="cancel separator"><a href="#cancel">Cancel</a></li>
</ul>

<div id="teaser">
	<div id="errorDiv" class="important" style="display: inherit">This is shell to showcase the future movies section, there are no movies in the DB yet.</div>
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