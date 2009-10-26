$(document).ready(function(){
	loadSettings();
	
	$("#accordion").accordion({autoHeight: false, collapsible: true, active: false});

	$("#userTable").tablesorter({
		headers: {
			1: { sorter: false },
			2: { sorter: false },
			3: { sorter: false }				
		},
		sortList: [[0,0]],
		textExtraction: function(node) {
            return node.childNodes[1].innerHTML; 
    	}
	});
});

$(document).ready(function(){//intialize add user dialog
	//TODO: check this function, complete add and remove user.
	//TODO: revise user change password. test it.
	
	var name = $("#username");
	var	passw = $("#passw");
	var	admin = $("#admin");
	var	allFields = $([]).add(name).add(passw).add(admin);
	var	tips = $("#validateTips");
	
	$("#dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 300,
		modal: true,
		buttons: {
			'Create an account': function() 
			{
				var bValid = true;
				allFields.removeClass('ui-state-error');

				bValid = bValid && checkLength(name,"username",3,16);
				bValid = bValid && checkLength(passw,"password",6,15);

				bValid = bValid && checkRegexp(name,/^[a-z]([0-9a-z_])+$/i,"Username may consist of a-z, 0-9, underscores, begin with a letter.");
				bValid = bValid && checkRegexp(passw,/^([0-9a-zA-Z])+$/,"Password field only allow : a-z 0-9");
				
				if (bValid) 
				{
					postData =  "a=addu";
					postData += "&u=" + name.val();
					postData += "&p=" + passw.val();						
					postData += "&adm=" + admin.attr('checked');
					postData += "&SID=" + SID;
					$.post("./exec.php", postData, addUser);
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
				$("#musicFoldersList").append("<option>" + $("#folderName").val() + "</option>");
				folders = new Array();
				
				$("#musicFoldersList option").each(function()
				{
					folders.push($(this).val());
				});
				
				postData = "a=set&key=musicFolders&value=" + JSON.stringify(folders);
				$.post("./server/dbuadm.php", postData, displayError);
				
				$(this).dialog('close');
			},
			'Cancel': function() 
			{
				$(this).dialog('close');
			}
		}			
	});
});

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
	postData = "a=get&keys=" + JSON.stringify(keysArr);
	$.post("./server/dbuadm.php", postData, fillOutSettings, 'json');
}

function saveSettings()
{
	settings = function(){
		this.keys = new Array();
		this.values = new Array();
		var thisvar = this;
		
		$(".settings").each(function()
		{
			thisvar.keys.push($(this)[0].id);
			thisvar.values.push($(this).val());
		});	
	};
	
	setObj = new settings();	
	//alert(JSON.stringify(setObj));
	postData = "a=set&data=" + JSON.stringify(setObj);
	$.post("./server/dbuadm.php", postData, displayError);
}

function changePassw()
{
	if ( ($("#reNewPassw").val() == "" ) || ( $("#newPassw").val() == "" ) || ( $("#oldPassw").val() == "" ) )
	   displayError("ERROR: Passwords can't be empty");
	else
    if ($("reNewPassw").value == $("newPassw").value)
	{
		postData = "a=cpassw&np=" + $("#newPassw").val() + "&op=" + $("#oldPassw").val();
		$.post("./server/dbuadm.php", postData, displayError);
	}
	else
		displayError("ERROR: Passwords don't match");
}

function saveUser(id)
{
	//alert($("#passw" + id).val());
	postData =  "a=saveu";
	postData += "&id=" + id;
	postData += "&p=" + $("#passw" + id).val();
	postData += "&adm=" + $("#admin" + id).attr('checked');
	postData += "&SID=" + SID;
	$.post("./server/dbuadm.php", postData, displayError);
}

function addUser(data)
{
	$('#userTable tbody').append(data);
	$("#userTable").trigger("update");
	var sorting = [[0,1]];
	$("#userTable").trigger("sorton",[sorting]); 
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
		document.location = "./index.php?p=createDB";
	}
}