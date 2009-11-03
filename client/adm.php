<?php
	if (!isset($sess_id))
	{
		header("Location: .");
		exit();
	}
	
	include './models/UsersDB.php';
	$usersDB = new UsersDB('./models/dbfiles/users.db'); 
?>
<script type="text/javascript" src="./client/js/lib/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="./client/js/lib/json2_mini.js"></script>
<script type="text/javascript" src="./client/js/adm.js"></script>

<div id="addFolderDiag" title="Add a folder to music library">
	<form>
		<fieldset>
			<label for="folderName">Folder Full Path:</label>
			<input type="text" id="folderName" />
		</fieldset>
	</form>
</div>

<div id="addUserDiag" title="Create new user">
	<p id="validateTips">All form fields are required.</p>
	<form>
		<fieldset>
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all" />
			<label for="passw">Password:</label>
			<input type="password" name="passw" id="passw" value="" class="text ui-widget-content ui-corner-all" />
			<label for="admin">Admin?&nbsp;</label>
			<input type="checkbox" name="admin" id="admin" class="ui-widget-content ui-corner-all" />
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
		<h3><a href="#">&nbsp;<img src="./client/images/passwadm.png" alt="User Administration Icon">&nbsp;Change Password</a></h3>
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
		<?php if ($_SESSION['userAdminLevel']): ?>
		<h3><a href="#">&nbsp;<img src="./client/images/useradm.png" alt="User Administration Icon">&nbsp;User Administration</a></h3>
		<div>
			<form class="yform">
				<fieldset>
					<br />
					<table id="userTable" class="tablesorter">
						<thead><tr>
							<th scope="col">Username</th>
							<th scope="col">Password</th>
							<th scope="col">Admin?</th>
							<th scope="col">&nbsp;</th>
						</tr></thead>
						<tbody>
						<?php
							$uArr = $usersDB->listUsers();
						
							for ($i = 0; $i < count($uArr); $i++)
							{
								$id = $uArr[$i]["user_id"];
								echo "<tr id='tr$id'>";
								echo "<td><input type='checkbox' name='userCheck$id' id='userCheck$id' value='$id' />";
								echo "<label for='userCheck$id' style='display: inline; '>&nbsp;&nbsp;";
								echo $uArr[$i]["user_name"]."</label></td>";
								echo "<td><input type='password' id='passw$id' /><span></span></td>";
								if ($uArr[$i]["user_admin_level"] == 1)
									echo "<td><input type='checkbox' id='admin$id' checked='checked' /><span></span></td>";
								else
									echo "<td><input type='checkbox' id='admin$id'/><span></span></td>";
								echo "<td><div class='type-button' style='margin: 0; '><input type='button' value='Save' onclick=\"saveUser('$id')\" /></div><span></span></td>";
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</fieldset>
				<fieldset>
					<div class="type-button">
						<input type="button" onclick="_addUser()" value="Add User" />
						<input type="button" onclick="_delUser()" value="Delete User" />
					</div>
				</fieldset>
				<!-- fieldset>
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
				</fieldset-->
			</form>
		</div>
		<h3><a href="#">&nbsp;<img src="./client/images/dbadm.png" alt="DB Administration Icon">&nbsp;Database Administration</a></h3>
		<div>
			<p>"Recreate Database" will delete the existing database and scan the music directories to recreate it. This takes time please be patient.</p>
			<form class="yform">
				<fieldset>
					<legend> Music Folders </legend>
					<div class="" style="float: left; width: 60%;">
						<select id="musicFoldersList" size="10" style="width: 100%">
						<?php
							$musicFolders = json_decode($settings->get("musicFolders"));
							//print_r($musicFolders);
							for ($i = 0; $i < count($musicFolders); $i++)
							{
								echo "<option>{$musicFolders[$i]}</option>\n";
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
		<h3><a href="#">&nbsp;<img src="./client/images/cog.png" alt="Settings Icon">&nbsp;Settings</a></h3>
		<div>
			<form class="yform">
				<fieldset>
					<legend> Settings </legend>
					<div class="type-text">
						<label for="baseURL">musicServer URL: </label>
						<input type="text" id="baseURL" class="settings" />
					</div>
					<!--div class="type-text">
						<label for="version">Version: </label>
						<input type="text" id="version" class="settings" />
					</div-->					
					<div class="type-button">
						<input type="button" id="btnSaveSettings" value="Save Settings" onclick="saveSettings()" />
					</div>										
				</fieldset>
			</form>
		</div>
		<?php endif; ?>
	</div>
</div>