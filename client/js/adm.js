$(document).ready(function(){
	loadSettings();
	
	$("#accordion").accordion({autoHeight: false, collapsible: true, active: false});

	$("#userTable").tablesorter({
		headers: {
			1: { sorter: false },
			2: { sorter: false },
			3: { sorter: false }				
		},
		sortList: [[0,0]]
	});
});

$(document).ready(function(){//intialize add user dialog
	var name = $("#userName"),
		password = $("#userPassword"),
		admin = $("#userAdmin"),
		allFields = $([]).add(name).add(admin).add(password),
		tips = $("#validateTips");

	function updateTips(t) {
		tips.text(t).effect("highlight",{},1500);
	}

	function checkLength(o,n,min,max) {

		if ( o.val().length > max || o.val().length < min ) {
			o.addClass('ui-state-error');
			updateTips("Length of " + n + " must be between "+min+" and "+max+".");
			return false;
		} else {
			return true;
		}

	}

	function checkRegexp(o,regexp,n) {

		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass('ui-state-error');
			updateTips(n);
			return false;
		} else {
			return true;
		}

	}
	
	$("#addUserDiag").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 280,
		modal: true,
		buttons: {
			'Create an account': function() 
			{
				var bValid = true;
				allFields.removeClass('ui-state-error');

				bValid = bValid && checkLength(name,"username",3,16);
				bValid = bValid && checkLength(password,"password",6,15);

				bValid = bValid && checkRegexp(name,/^[a-z]([0-9a-z_])+$/i,"Username may consist of a-z, 0-9, underscores, begin with a letter.");
				bValid = bValid && checkRegexp(password,/^([0-9a-zA-Z])+$/,"Password field only allow : a-z 0-9");
				
				if (bValid) 
				{
					postData =  "a=addu";
					postData += "&u=" + name.val();
					postData += "&p=" + password.val();
					//alert(admin.attr('checked'));
					postData += "&adm=" + admin.attr('checked');
					postData += "&SID=" + SID;
					$.post("./server/adm.php", postData, addUser, "json");
					$(this).dialog('close');
				}
			},
			Cancel: function() 
			{
				$(this).dialog('close');
			}
		},
		close: function() 
		{
			allFields.val('').removeClass('ui-state-error');
		}
	});
});

$(document).ready(function(){//add folder dialog init
	$("#addFolderDiag").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 150,
		modal: true,
		buttons: {
			'Add this folder': function() 
			{
				postData = "a=checkFolder&f=" + $("#folderName").val() + "&SID=" + SID;
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
		height: 160,
		width: 400,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		},
		buttons: {
			'Yes. I\'m sure': function() {
				postData = "a=delU&id=" + $(this).dialog('option', 'userID') + "&SID=" + SID;
				$.post("./server/adm.php", postData, delUser, 'json');
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
});

function addFolder(data)
{
	if (data.isError)
	{
		displayError(data.resultStr);
	}
	else
	{
		$("#musicFoldersList").append("<option>" + data.resultStr + "</option>");
		folders = new Array();
		
		$("#musicFoldersList option").each(function()
		{
			folders.push($(this).val());
		});
		
		keys = new Array("musicFolders");
		values = new Array(JSON.stringify(folders));
		
		setObj = new settings(keys, values);	
		//alert(JSON.stringify(setObj));
		postData = "a=set&data=" + JSON.stringify(setObj) + "&SID=" + SID;
		$.post("./server/adm.php", postData, displayError);
	}
}

function fillOutSettings(data)
{
	$(".settings").each(function()
	{
		$(this).val(data[$(this)[0].id]);
	});
}

function loadSettings()
{
	keysArr = new Array();
	$(".settings").each(function()
	{
		keysArr.push($(this)[0].id);
	});
	//alert(keysArr[0]);
	postData = "a=get&keys=" + JSON.stringify(keysArr) + "&SID=" + SID;
	$.post("./server/adm.php", postData, fillOutSettings, 'json');
}

var settings = function(pKeys, pValues){
	this.keys = new Array();
	this.values = new Array();
	var thisvar = this;
	
	if (pKeys && pValues)
	{
		for(i = 0; i < pKeys.length; i++)
		{
			thisvar.keys.push(pKeys[i]);
			thisvar.values.push(pValues[i]);
		}
	}
	else
	{
		$(".settings").each(function()
		{
			thisvar.keys.push($(this)[0].id);
			thisvar.values.push($(this).val());
		});
	}
};

function saveSettings()
{
	setObj = new settings(null, null);	
	//alert(JSON.stringify(setObj));
	postData = "a=set&data=" + JSON.stringify(setObj) + "&SID=" + SID;
	$.post("./server/adm.php", postData, displayError);
}

function changePassw()
{
	if ( ($("#reNewPassw").val() == "" ) || ( $("#newPassw").val() == "" ) || ( $("#oldPassw").val() == "" ) )
	   displayError("ERROR: Passwords can't be empty");
	else
    if ($("#reNewPassw").val() == $("#newPassw").val())
	{
		postData = "a=cpassw&np=" + $("#newPassw").val() + "&op=" + $("#oldPassw").val() + "&SID=" + SID;
		$.post("./server/adm.php", postData, displayError);
	}
	else
		displayError("ERROR: Passwords don't match");
}

function saveUser(id)
{
	//alert($("#passw" + id).val());
	postData =  "a=saveu";
	postData += "&id=" + id;
	postData += "&un=" + $("#userName" + id).html();
	postData += "&p=" + $("#passw" + id).val();
	postData += "&adm=" + $("#admin" + id).attr('checked');
	postData += "&SID=" + SID;
	$.post("./server/adm.php", postData, displayError);
}

function addUser(data)
{
	if (data.isError)
	{
		displayError(data.resultStr);
	}
	else
	{
		$('#userTable tbody').append(data.resultStr);
		$("#userTable").trigger("update");
		var sorting = [[0,1]];
		$("#userTable").trigger("sorton",[sorting]);
	}
}

function _addUser()
{
	$("#addUserDiag").dialog('open');
}

function createDB()
{
	res = confirm("This process takes some time based on how much music you have, network speed, etc. Please be patient.");
	if (res)
	{
		document.location = "./?p=createDB";
	}
}

function removeFolder()
{
	index = $("#musicFoldersList").attr("selectedIndex");
	$("#musicFoldersList option:eq("+ index +")").remove();
	folders = new Array();
	
	$("#musicFoldersList option").each(function()
	{
		folders.push($(this).val());
	});
	
	keys = new Array("musicFolders");
	values = new Array(JSON.stringify(folders));
	
	setObj = new settings(keys, values);	
	//alert(JSON.stringify(setObj));
	postData = "a=set&data=" + JSON.stringify(setObj) + "&SID=" + SID;
	$.post("./server/adm.php", postData, displayError);
}

function _delUser(id)
{
	$("#delUserConfDialog").dialog('option', 'userID', id).dialog('open');
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