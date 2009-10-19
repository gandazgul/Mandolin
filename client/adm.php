<?php
	if (!isset($sess_id))
	{
		header("Location: .");
		exit();
	}
?>
<script type="text/javascript" src="./js/adm.js"></script>

<div id="addFolderDiag" title="Add a folder to music library">
	<form>
		<fieldset>
			<label for="folderName">Folder Full Path:</label>
			<input type="text" id="folderName" />
		</fieldset>
	</form>
</div>

<div id="nav">
	<!-- skiplink anchor: navigation -->
	<a id="navigation" name="navigation"></a>
	<div class="hlist">
		<!-- main navigation: horizontal list -->
		<ul>
			<li><a href=".">Music</a></li>
			<li><a href="./?p=pl">Music Playlists</a></li>
			<li><a href="./?p=movies">Movies</a></li>
			<li class="active"><strong>Aministration</strong></li>
			<li><a href="./?p=about">About</a></li>
			<li><a href="./?p=logout">Logout</a></li>
		</ul>
	</div>
</div>
<div id="teaser">
	<div id="errorDiv" class="important" style="display: none"></div>
</div>
<div id="main" style="padding: 0 20px;">
	<div id="accordion">
		<h3><a href="#">Change Password</a></h3>
		<div>
			<form class="yform" style="margin-bottom: 0px">
				<fieldset>
					<div class="type-text">
						<label for="oldPassw">Old password:</label>
						<input type="password" id="oldPassw" />
					</div>
					<div class="type-text">
						<label for="oldPassw">New password:</label>
						<input type="password" id="newPassw" />
					</div>
					<div class="type-text">
						<label for="oldPassw">Re-Type New password:</label>
						<input type="password" id="reNewPassw" />						
					</div>
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
					<legend> Enter New User Information </legend>
					<div class="type-text">
						<label for="username">Username:</label>
						<input type="text" id="username" />
					</div>
					<div class="type-text">
						<label for="passw">Password:</label>
						<input type="text" id="passw" />
					</div>
					<div class="type-text">
						<label for="rePassw">Re-Type Password:</label>
						<input type="text" id="rePassw" />
					</div>
				</fieldset>
				<fieldset>
					<legend> Select permission level </legend>
					<div class="type-check">
						<input type="radio" name="adminLvl" id="adminLvl0" value="0" />&nbsp;<label for="adminLvl0">Administrator</label>
						<div class="message">
							(Can delete, rename songs and users and 
							also update or recreate the database)
						</div>
					</div>
					<div class="type-check">
						<input type="radio" name="adminLvl" id="adminLvl1" value="1" />&nbsp;<label for="adminLvl1">Mantainer</label>
						<div class="message">
							(Can rename songs, change passwords of users 
							and update the database)
						</div>
					</div>
					<div class="type-check">
						<input type="radio" name="adminLvl" id="adminLvl2" value="2" checked="checked" />&nbsp;<label for="adminLvl2">User</label>
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
			<p>"Recreate Database" will delete the existing database and scan the music directories to recreate it. This takes time please be patient.</p>
			<form class="yform">
				<fieldset>
					<legend> Music Folders </legend>
					<div class="" style="float: left; width: 60%;">
						<select id="musicFoldersList" size="10" style="width: 100%">
						<?php
							$musicFolders = $settings->get("musicFolders");
							for ($i = 0; $i < count($musicFolders); $i++)
							{
								echo "<option>{$musicFolders[$i]}</option>";
							}
						?>
						</select>
					</div>
					<div class="type-button" style="float: left; margin-top: 0; margin-left: 10px;">
						<input type="button" id="btnNewFolder" value="Add Folder" onclick="$('#addFolderDiag').dialog('open')" /><br />
						<input type="button" id="btnRemoveFolder" value="Remove Folder" onclick="removeFolder()" /><br />
						<input type="button" value="Recreate Database" onclick='createDB()'>
					</div>
					<div style="width: 100%; float: left; height: 10px;"><!-- SEPARATOR --></div>								
				</fieldset>
			</form>
		</div>
		<h3><a href="#">Settings</a></h3>
		<div>
			<form class="yform">
				<fieldset>
					<legend> Database settings </legend>
					<div class="type-text">
						<label for="serverURL">musicServer URL: </label>
						<input type="text" id="serverURL" />
					</div>
					<div class="type-text">
						<label for="serverURL">Music Library path: </label>
						<input type="text" id="mlpath" />
					</div>
					<div class="type-button">
						<input type="button" id="btnSaveSettings" value="Save Settings" onclick="saveSettings()" />
					</div>										
				</fieldset>
			</form>
		</div>
	</div>
</div>