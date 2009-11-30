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
<style type="text/css">
table {
	border-bottom: none;
	border-top: none;
	margin-bottom: none;
	
	width: auto;
	border-collapse: collapse;
}

tbody td {
	border-bottom: none;
}

.ui-form label.block, .ui-form input.text { display:block; }
.ui-form input.text { margin-bottom:12px; width:95%; padding: .4em; }
.ui-form fieldset { padding:0; border:0; }
.ui-button { outline: 0; margin:0; padding: .4em 1em .5em; text-decoration:none;  !important; cursor:pointer; position: relative; text-align: center; }
.ui-dialog .ui-state-highlight, .ui-dialog .ui-state-error { padding: .3em;  }

</style>
<div id="addFolderDiag" title="Add a folder to music library">
	<br />
	<label for="folderName">Folder Full Path:</label>
	<input type="text" id="folderName" />
</div>
<div id="addUserDiag" title="Create new user">
	<p id="validateTips">All form fields are required.</p>

	<form class="ui-form">
	<fieldset>
		<label for="userName" class="block">Name</label>
		<input type="text" name="userName" id="userName" class="text ui-widget-content ui-corner-all" />
		<label for="userPassword" class="block">Password</label>
		<input type="password" name="userPassword" id="userPassword" class="text ui-widget-content ui-corner-all" />
		<label for="userAdmin">Admin?</label>
		<input type="checkbox" name="userAdmin" id="userAdmin" class="ui-widget-content ui-corner-all" />
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
								echo "<td><div class='type-button' style='margin: 0; '><input type='button' value='Save' onclick=\"saveUser('$id')\" />&nbsp;<input type='button' onclick=\"_delUser('$id')\" value='Delete' /></div><span></span></td>";
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</fieldset>
				<fieldset>
					<div class="type-button">
						<input type="button" onclick="_addUser()" value="Add User" />
					</div>
				</fieldset>
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