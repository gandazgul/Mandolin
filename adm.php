<?php
	if (!isset($sess_id))
	{
		header("Location: ./index.php");
		exit();
	}
?>
<script type="text/javascript">
	<?php include_once("./js/adm.js"); ?>
</script>
<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href="./index.php">Search/Browse</a></li>
			<li><a href="./index.php">My Playlists</a></li>
			<li class="active"><strong>Aministration</strong></li>
			<li><a href="./index.php?p=about">About</a></li>
			<li><a href="./logout.php">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv"></div>
</div>
<div id="main" style="padding: 0 20px;">
	<div id="accordion">
		<h3><a href="#">Change Password</a></h3>
		<div>
			<form class="yform" style="margin-bottom: 0px">
				<fieldset><div class="type-text">
					<label for="oldPassw">Old password:</label>
					<input type="password" id="oldPassw" />
					<label for="oldPassw">New password:</label>
					<input type="password" id="newPassw" />
					<label for="oldPassw">Re-Type New password:</label>
					<input type="password" id="reNewPassw" />						
				</div></fieldset>
				<fieldset>
					<div class="type-button">
						<input type="button" value="Change Password" onclick="changePassw()">
					</div>
				</fieldset>
			</form>
		</div>
		<h3><a href="#">Add New User</a></h3>
		<div>
			<form class="yform">
				<fieldset>
					<legend>Enter New User Information</legend>
					<div class="type-text">
						<div id="userMsg"></div>
						<label for="username">Username:</label>
						<input type="text" id="username" />
						<label for="passw">Password:</label>
						<input type="text" id="passw" />
						<label for="rePassw">Re-Type Password:</label>
						<input type="text" id="rePassw" />
					</div>
				</fieldset>
				<fieldset>
					<legend>Select permission level</legend>
					<div class="type-check">
						<input type="radio" name="adminLvl" value="0" />Administrator
						<div class="message">
							(Can delete, rename songs and users and 
							also update or recreate the database)
						</div>
						<input type="radio" name="adminLvl" value="1" />Mantainer
						<div class="message">
							(Can rename songs, change passwords of users 
							and update the database)
						</div>
						<input type="radio" name="adminLvl" value="2" checked="checked" />User
					</div>
				</fieldset>
				<fieldset>
					<div class="type-button">
						<input type="button" value="Add user" onClick="addUser()" />
					</div>
				</fieldset>
			</form>
		</div>
		<h3><a href="#">Database Administration</a></h3>
		<div>
			<p>Recreate database (faster than update but will probably invalidate the saved playlists) 
			This will delete the existing database and scan the music directory to recreate it.</p>
			<input type="button" value="Create DB" onclick='createDB()'>
			<br /><br />
			<p>Automatic update (slower but will maintain saved playlists) - 
			This will scan the music directory looking for new stuff that is not on the database.</p>
			<input type="button" value="Update DB" onclick='updateDB()'>			
		</div>
	</div>
</div>