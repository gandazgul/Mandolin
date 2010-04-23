<?php
	if (!isset($sess_id))
	{
		header("Location: ../?p=adm");
		exit();
	}
	
	include './models/UsersDB.php';
	$usersDB = new UsersDB('./models/dbfiles/users.db');
?>
<script type="text/javascript">
	<?php echo "var userData = ".json_encode($usersDB->listUsers()).";"; ?>
</script>
<textarea id="userRow" style="display: none;">
	<tr id='tr{$T.user_id}'>
		<td>
			<input type='checkbox' name='userCheck{$T.user_id}' id='userCheck{$T.user_id}' value='{$T.user_id}' />
			<span id='userName{$T.user_id}'>{$T.user_name}</span>
		</td>
		<td><input type='password' id='passw{$T.user_id}' class='ui-widget-content ui-corner-all text-no-margin' /></td>
		<td><input type='checkbox' id='admin{$T.user_id}' {#if ($T.user_admin_level == 1) || ($T.user_admin_level == 'TRUE')}checked='checked'{#/if} /></td>
		<td>
			<button type="button" value="{$T.user_id}" class="btnSaveUser">Save</button>&nbsp;
			<button type="button" value="{$T.user_id}" class="btnDelUser">Delete</button>
		</td>
	</tr>
</textarea>
<div id="addFolderDiag" title="Add a folder to music library">
	<form action="" class="ui-form ui-widget top">
		<label for="folderName">Folder Full Path:</label>
		<input type="text" id="folderName" class="text ui-widget-content ui-corner-all" />
	</form>
</div>
<div id="addUserDiag" title="Create new user">
	<p id="udValidateTips">All form fields are required.</p>
	<form class="ui-form ui-widget" action="">
		<label for="userName">Username</label>
		<input type="text" name="userName" id="userName" class="text ui-widget-content ui-corner-all" />
		<label for="userPassword">Password</label>
		<input type="password" name="userPassword" id="userPassword" class="text ui-widget-content ui-corner-all" />
		<label for="userAdmin">Admin?</label>
		<input type="checkbox" name="userAdmin" id="userAdmin" class="ui-widget-content ui-corner-all" />
	</form>
</div>
<div id="importUsersDlg" title="Import users">
	<table id="importUserTable" class="tablesorter">
		<thead><tr>
			<th scope="col">Username</th>
			<th scope="col"></th>
		</tr></thead>
		<tbody id="importUserTableBody"></tbody>
		<textarea id="importUserTempl" style="display: none;">
			{#foreach $T as user}
				<tr id='tr{$T.user.user_id}'>
					<td>
						<input type='checkbox' name='userCheck{$T.user.user_id}' id='userCheck{$T.user.user_id}' value='{$T.user.user_id}' />
						<span id='userName{$T.user.user_id}'>{$T.user.user_name}</span>
					</td>
					<td>
						<input id="userData{$T.user.user_id}" type="hidden" value='{#ldelim}"user_password":"{$T.user.user_password}","user_admin_level":"{$T.user.user_admin_level}"{#rdelim}' />
					</td>
				</tr>
			{#/for}
		</textarea>
	</table>
</div>
<div id="delUserConfDialog" title="Delete user">
	Deleting a user is permanent. To reactivate this user you will have to add him to the DB again. Are you sure you want to proceed?
</div>
<div id="errorDiv" class="important"></div>
<!--div id="main"-->
	<div id="admAccordion">
		<h3><a href="#">&nbsp;<img src="./client/images/passwadm.png" alt="User Administration Icon">&nbsp;Change Password</a></h3>
		<div>
			<form action="" class="ui-form ui-widget">
				<fieldset class="ui-widget-content ui-corner-all">
					<label for="oldPassw">Old password:</label>
					<input type="password" id="oldPassw" class="text ui-widget-content ui-corner-all" />
					<label for="oldPassw">New password:</label>
					<input type="password" id="newPassw" class="text ui-widget-content ui-corner-all" />
					<label for="oldPassw">Re-Type New password:</label>
					<input type="password" id="reNewPassw" class="text ui-widget-content ui-corner-all" />
				</fieldset>
				<fieldset class="ui-widget-content ui-corner-all top">
					<button type="button" id="btnChangePassw">Change Password</button>
				</fieldset>
			</form>
		</div>
		<h3><a href="#">&nbsp;<img src="./client/images/user_settings.png" alt="Settings Icon">&nbsp;User Settings</a></h3>
		<div>
			<form action="" class="ui-form ui-widget">
				<fieldset class="ui-widget-content ui-corner-all">
					<label for="plFormat">What format do you want your playlists to be? (Default: XSPF) </label>
					<select id="plFormat" class="usettings ui-widget-content ui-corner-all text">
						<option value="xspf">XSPF - XML Playlist, www.xspf.org</option>
						<option value="m3u">M3U - For Windows Media Player. www.videolan.org</option>
					</select>
					<label for="bitrate">Bitrate for the stream: (This will only be used if LAME has been setup) </label>
					<select id="bitrate" class="usettings ui-widget-content ui-corner-all text">
						<option value="64">64 bits (Not Recommended)</option>
						<option value="80">80 bits (Mobile Devices)</option>
						<option value="96">96 bits</option>
						<option value="128">128 bits (Most Popular)</option>
						<option value="192">160 bits</option>
						<option value="192">192 bits (Becoming Popular)</option>
						<option value="224">224 bits</option>
						<option value="256">256 bits</option>
						<option value="320">320 bits (Higest MP3 quality)</option>
					</select>
					<!--label for="version">Version: </label>
					<input type="text" id="version" class="usettings ui-widget-content ui-corner-all" /-->
				</fieldset>
				<fieldset class="ui-widget-content ui-corner-all top">
					<button type="button" id="btnSaveUSettings">Save Settings</button>
				</fieldset>
			</form>
		</div>
		<?php if ($_SESSION['userAdminLevel']): ?>
		<h3><a href="#">&nbsp;<img src="./client/images/useradm.png" alt="User Administration Icon">&nbsp;User Administration</a></h3>
		<div>
			<form action="" class="ui-form ui-widget">
				<fieldset class="ui-widget-content ui-corner-all">
					<table id="userTable" class="tablesorter">
						<thead><tr>
							<th scope="col">Username</th>
							<th scope="col">Password</th>
							<th scope="col">Admin?</th>
							<th scope="col">&nbsp;</th>
						</tr></thead>
						<tbody id="usersTableBody"></tbody>
						<textarea id="usersTempl" style="display: none;">
							{#foreach $T as user}
								{#include userRow root=$T.user}
							{#/for}
						</textarea>
					</table>
				</fieldset>
				<fieldset class="ui-widget-content ui-corner-all top">
					<button type="button" id="btnAddUser">Add User</button>
					<button type="button" id="btnImportUsers">Import CSV User List</button>
				</fieldset>
			</form>
		</div>
		<h3><a href="#">&nbsp;<img src="./client/images/dbadm.png" alt="DB Administration Icon">&nbsp;Music Library</a></h3>
		<div>
			<form action="" class="ui-form ui-widget">
				<fieldset class="ui-widget-content ui-corner-all">
					<p>"Recreate Database" will delete the existing database and scan the music directories to recreate it. This takes time please be patient.</p>
					<div id="musicFolders">
						<select id="musicFoldersList" size="10" class="ui-widget-content subcolumns">
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
				</fieldset>
				<fieldset class="ui-widget-content ui-corner-all top">
					<button type="button" id="btnAddFolder">Add Folder</button>
					<button type="button" id="btnRemoveFolder">Remove Folder</button>
					<button type="button" id="btnRecreateDB">Recreate Database</button>
					<span id="loading"><img alt='Loading...' src='./client/images/ajax-loader.gif' /></span>
				</fieldset>
			</form>
		</div>
		<h3><a href="#">&nbsp;<img src="./client/images/wrench.png" alt="Settings Icon">&nbsp;System Settings - Don't change these settings unless you absolutely know what you are doing.</a></h3>
		<div>
			<form action="" class="ui-form ui-widget">
				<fieldset class="ui-widget-content ui-corner-all">					
					<label for="baseURL">Where is Mandolin currently hosted? (URL) </label>
					<input type="text" id="baseURL" class="settings ui-widget-content ui-corner-all text" />
					<label for="keyLastsFor">How long (Milisecs) should playlists last? </label>
					<input type="text" id="keyLastsFor" class="settings ui-widget-content ui-corner-all text" />
					<label for="lameCMD">Where is LAME located? (Leave blank if you dont want to use it. If you dont install LAME then OGGDEC and FLAC are not needed; also you will have problems streaming, high bitrate mp3/ogg and flac files.) </label>
					<input type="text" id="lameCMD" class="settings ui-widget-content ui-corner-all text" />
					<label for="oggCMD">Where is OGGDEC located? </label>
					<input type="text" id="oggCMD" class="settings ui-widget-content ui-corner-all text" />
					<label for="flacCMD">Where is FLAC located? </label>
					<input type="text" id="flacCMD" class="settings ui-widget-content ui-corner-all text" />
					<!--label for="version">Version: </label>
					<input type="text" id="version" class="settings ui-widget-content ui-corner-all text" /-->
				</fieldset>
				<fieldset class="ui-widget-content ui-corner-all top">
					<button type="button" id="btnSaveSettings">Save Settings</button>
				</fieldset>
			</form>
		</div>
		<?php endif; ?>
	</div>
<!--/div-->