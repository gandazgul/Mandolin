$(document).ready(function(){
	loadSettings();
	loadUSettings();

	$("#admAccordion").accordion({autoHeight: false, collapsible: true, active: false}).css('margin', '1em 0');

	var vUserRow = $.createTemplate($("#userRow").text());
	$("#usersTableBody").setTemplate($('#usersTempl').text(), {userRow: vUserRow});
	$("#usersTableBody").processTemplate(userData);

	$("#userTable").tablesorter({
		headers: {
			1: {sorter: false},
			2: {sorter: false},
			3: {sorter: false},
			4: {sorter: false}
		},
		sortList: [[0,0]]
	});

	function updateTips(t) {
		$("#udValidateTips").text(t).effect("highlight",{},1500);
	}

	function checkLength(o,n,min,max)
	{
		if ( o.val().length > max || o.val().length < min )
		{
			o.addClass('ui-state-error');
			updateTips("Length of " + n + " must be between "+min+" and "+max+".");
			return false;
		}
		else
		{
			return true;
		}

	}

	function checkRegexp(o, regexp, n) {

		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass('ui-state-error');
			updateTips(n);
			return false;
		} else {
			return true;
		}

	}

	//intialize add user dialog
	$("#addUserDiag").dialog({
		bgiframe: true,
		autoOpen: false,
		resizable: false,
		
		modal: true,
		buttons: {
			'Create an account': function() 
			{
				var name = $("#userName"),
				password = $("#userPassword"),
				admin = $("#userAdmin"),
				allFields = $([]).add(name).add(admin).add(password);

				var bValid = true;
				allFields.removeClass('ui-state-error');

				bValid = bValid && checkLength(name,"username",3,16);
				bValid = bValid && checkLength(password,"password",6,15);

				bValid = bValid && checkRegexp(name,/^[a-z]([0-9a-z_])+$/i,"Username may consist of a-z, 0-9, underscores, begin with a letter.");
				bValid = bValid && checkRegexp(password,/^([0-9a-zA-Z])+$/,"Password field only allow : a-z 0-9");

				if (bValid)
				{
					var postData =  "a=addu&u=" + name.val() + "&p=" + password.val() + "&adm=" + admin.attr('checked') + "&SID=" + SID;
					$.post("./server/adm.php", postData, addUser, "json");
					$(this).dialog('close');
				}
				allFields.val('');
			},
			Cancel: function() 
			{
				$(this).dialog('close');
			}
		}
	});

	//import user dialog init
	$("#importUsersDlg").dialog({
		bgiframe: true,
		resizable: false,
		autoOpen: false,
		modal: true,
		buttons: {
			'Start Import': function()
			{
				$("#importUserTableBody tr").each(function(index, tr){

					if($(this).children("td:eq(0)").children("input").attr("checked"))
					{
						uID = $(this).children("td:eq(0)").children("span").html();
						//alert(uID);
						uData = $(this).children("td:eq(1)").children("input").val();
						//alert(uData);
						data = JSON.parse(uData);
						var postData =  "a=addu&u=" + uID + "&p=" + data.user_password + "&adm=" + data.user_admin_level + "&SID=" + SID;
						//alert(postData);
						$.post("./server/adm.php", postData, addUser, "json");
					}
				});

				$(this).dialog('close');
			},
			'Cancel': function()
			{
				$(this).dialog('close');
			}
		}
	});

	//add folder dialog init
	$("#addFolderDiag").dialog({
		bgiframe: true,
		resizable: false,
		autoOpen: false,
		modal: true,
		buttons: {
			'Add this folder': function() 
			{
				$("#loading").show();
				var postData = "a=addFolderToDB&f=" + $("#folderName").val() + "&SID=" + SID;
				$.post("./server/adm.php", postData, addFolder, 'json');
				
				$(this).dialog('close');
			},
			'Cancel': function() 
			{
				$(this).dialog('close');
			}
		}			
	});
	
	$("#delUserConfDialog").dialog({
		bgiframe: true,
		resizable: false,
		autoOpen: false,
		height: 165,
		width: 400,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		},
		buttons: {
			'Yes. I\'m sure': function() {
				var postData = "a=delU&id=" + $(this).dialog('option', 'userID') + "&SID=" + SID;
				$.post("./server/adm.php", postData, delUser, 'json');
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
	//import users from csv --------------------------------------------------------------------------------------------------------------------------
	$("#btnImportUsers").button({
		icons: {
			primary: 'ui-icon-user-import'
		}
	});
	var button = $('#btnImportUsers .ui-button-text'), interval;

	new AjaxUpload($('#btnImportUsers'), {
		action: './server/adm.php?a=post&SID=' + SID,
		name: 'usersFile',
		autoSubmit: true,
		onSubmit : function(file, ext){
			// change button text, when user selects file
			button.text('Uploading');

			// If you want to allow uploading only 1 file at time,
			// you can disable upload button
			this.disable();

			// Uploding -> Uploading. -> Uploading...
			interval = window.setInterval(function(){
				var text = button.text();
				if (text.length < 13){
					button.text(text + '.');
				} else {
					button.text('Uploading');
				}
			}, 200);
		},
		onComplete: function(file, response){
			button.text('Import CSV User List');
			window.clearInterval(interval);
			this.enable();

			usersArr = JSON.parse(response);
			if (usersArr.isError)
			{
				displayError(usersArr.strResult, 'error');
			}
			else
			{
				$("#importUserTableBody").setTemplateElement('importUserTempl');
				$("#importUserTableBody").processTemplate(usersArr.strResult);
				$("#importUsersDlg").dialog('open');
			}			
		}
	});
	//Add user --------------------------------------------------------------------------------------------------------------------------------
	$("#btnAddUser").button({
		icons: {
			primary: 'ui-icon-user-add'
		}
	}).click(function(){
		$("#validateTips").html("All form fields are required.");
		$("#addUserDiag").dialog('open');
	});
	//save user info --------------------------------------------------------------------------------------------------------------------------------
	$(".btnSaveUser").button({
		icons: {
			primary: 'ui-icon-user-save'
		}
	}).click(function(){
		var postData =  "a=saveu&id=" + this.value + "&un=" + $("#userName" + this.value).html() + "&p=" + $("#passw" + this.value).val();
		postData += "&adm=" + $("#admin" + this.value).attr('checked');
		postData += "&SID=" + SID;
		alert(postData);
		$.post("./server/adm.php", postData, displayMessage);
	});
	//delete user --------------------------------------------------------------------------------------------------------------------------------
	$(".btnDelUser").button({
		icons: {
			primary: 'ui-icon-user-delete'
		}
	}).click(function(){
		$("#delUserConfDialog").dialog('option', 'userID', this.value).dialog('open');
	});
	//change password  --------------------------------------------------------------------------------------------------------------------------------
	$("#btnChangePassw").button({
		icons: {
			primary: 'ui-icon-ok'
		}
	}).click(function(){
		if ( ($("#reNewPassw").val() == "" ) || ( $("#newPassw").val() == "" ) || ( $("#oldPassw").val() == "" ) )
		   displayError("ERROR: Passwords can't be empty", 'error');
		else
		if ($("#reNewPassw").val() == $("#newPassw").val())
		{
			var postData = "a=cpassw&np=" + $("#newPassw").val() + "&op=" + $("#oldPassw").val() + "&SID=" + SID;
			$.post("./server/adm.php", postData, displayMessage);
		}
		else
			displayError("ERROR: Passwords don't match", 'error');
	});
	//user settings --------------------------------------------------------------------------------------------------------------------------------
	$("#btnSaveUSettings").button({
		icons: {
			primary: 'ui-icon-ok'
		}
	}).click(function(){
		var setObj = new settings(null, null, "usettings");
		var postData = "a=uset&data=" + JSON.stringify(setObj) + "&SID=" + SID;
		$.post("./server/adm.php", postData, displayMessage, 'json');
	});
	//add folder --------------------------------------------------------------------------------------------------------------------------------
	$("#btnAddFolder").button({
		icons: {
			primary: 'ui-icon-add-folder'
		}
	}).click(function(){
		$('#addFolderDiag').dialog('open');
	});
	//remove folder  --------------------------------------------------------------------------------------------------------------------------------
	$("#btnRemoveFolder").button({
		icons: {
			primary: 'ui-icon-delete-folder'
		}
	}).click(function(){
		var index = $("#musicFoldersList").attr("selectedIndex");
		$("#musicFoldersList option:eq("+ index +")").remove();
		var folders = new Array();

		$("#musicFoldersList option").each(function()
		{
			folders.push($(this).val());
		});

		var keys = new Array("musicFolders");
		var values = new Array(JSON.stringify(folders));

		var setObj = new settings(keys, values);
		//alert(JSON.stringify(setObj));
		var postData = "a=set&data=" + JSON.stringify(setObj) + "&SID=" + SID;
		$.post("./server/adm.php", postData, displayError);
	});
	//recreate DB  --------------------------------------------------------------------------------------------------------------------------------
	$("#btnRecreateDB").button({
		icons: {
			primary: 'ui-icon-recreate-db'
		}
	}).click(function(){
		var res = confirm("This process takes some time based on how much music you have, network speed, etc. Please be patient.");
		if (res)
		{
			document.location = "./?p=createDB";
		}
	});
	//save system settings  --------------------------------------------------------------------------------------------------------------------------------
	$("#btnSaveSettings").button({
		icons: {
			primary: 'ui-icon-ok'
		}
	}).click(function(){
		var setObj = new settings(null, null, "settings");
		//alert(JSON.stringify(setObj));
		var postData = "a=set&data=" + JSON.stringify(setObj) + "&SID=" + SID;
		$.post("./server/adm.php", postData, displayError);
	});
});

function addFolder(data)
{
	$("#loading").hide();
	if (data.isError)
	{
		displayError(data.resultStr);
	}
	else
	{
		$("#musicFoldersList").append("<option>" + data.resultStr + "</option>");
		var folders = new Array();
		
		$("#musicFoldersList option").each(function()
		{
			folders.push($(this).val());
		});
		
		var keys = new Array("musicFolders");
		var values = new Array(JSON.stringify(folders));
		
		var setObj = new settings(keys, values);
		//alert(JSON.stringify(setObj));
		var postData = "a=set&data=" + JSON.stringify(setObj) + "&SID=" + SID;
		$.post("./server/adm.php", postData, displayError);
	}
}

function _loadSettings(data)
{
	$(".settings").each(function()
	{
		$(this).val(data[$(this)[0].id]);
	});
}

function _loadUSettings(data)
{
	if (data.isError)
		displayError(data.resultStr);
	else
	{
		$(".usettings").each(function()
		{
			$(this).val(data.resultStr[$(this).attr('id')]);
		});
	}
}

function loadUSettings()
{
	var keysArr = new Array();
	$(".usettings").each(function()
	{
		keysArr.push($(this)[0].id);
	});
	//alert(keysArr[0]);
	var postData = "a=uget&keys=" + JSON.stringify(keysArr) + "&SID=" + SID;
	$.post("./server/adm.php", postData, _loadUSettings, 'json');
}

function loadSettings()
{
	var keysArr = new Array();
	$(".settings").each(function()
	{
		keysArr.push($(this)[0].id);
	});
	//alert(keysArr[0]);
	var postData = "a=get&keys=" + JSON.stringify(keysArr) + "&SID=" + SID;
	$.post("./server/adm.php", postData, _loadSettings, 'json');
}

var settings = function(pKeys, pValues, className){
	this.keys = new Array();
	this.values = new Array();
	var thisvar = this;
	
	if (pKeys && pValues)
	{
		for(var i = 0; i < pKeys.length; i++)
		{
			thisvar.keys.push(pKeys[i]);
			thisvar.values.push(pValues[i]);
		}
	}
	else
	{
		$("." + className).each(function()
		{
			//alert($(this)[0].id);
			thisvar.keys.push($(this)[0].id);
			thisvar.values.push($(this).val());
		});
	}
};

function addUser(data)
{
	if (data.isError)
	{
		displayError(data.resultStr);
	}
	else
	{
		var div = $("<div>");
		div.setTemplate($('#userRow').text());
		div.processTemplate(data.resultStr);
		$('#usersTableBody').append(div.html());
		$("#userTable").trigger("update");
		var sorting = [[0,1]];
		$("#userTable").trigger("sorton",[sorting]);
	}
}

function delUser(data)
{
	if (data.isError)
		displayError(data.resultStr);
	else
	{
		$('#tr' + data.resultStr).remove();
		$("#userTable").trigger("update");
		var sorting = [[0,1]];
		$("#userTable").trigger("sorton",[sorting]);
	}
}