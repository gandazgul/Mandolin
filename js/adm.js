$(document).ready(function(){
	$("#accordion").accordion({autoHeight: false, collapsible: true, active: false});

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
				
				postData = "a=folders&musicFolders=" + JSON.stringify(folders);
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

function saveSettings()
{
	
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

// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
function getCheckedValue(radioObj) 
{
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) 
	{
		if(radioObj[i].checked) 
		{
			return radioObj[i].value;
		}
	}
	return "";
}

function addUser()
{
	strUser = $("#username").val();
	strPassword = $("#passw").val();
	rePassw = $("#rePassw").val();
	
	radioObj = document.getElementsByName('adminLvl');
	adminLvl = getCheckedValue(radioObj);
	if ( (strPassword == "") || (rePassw == "") )
		displayError("ERROR: Passwords can't be empty");
	else	
	if ( strPassword == rePassw )
	{
		postData = "a=nuser";
		postData += "&usr=" + $("#username").val();
		postData += "&pw=" + $("#passw").val();
		//alert(getCheckedValue(document.getElementsByName('adminLvl')));
		postData += "&adm=" + getCheckedValue(document.getElementsByName('adminLvl'));
		$.post("./server/dbuadm.php", postData, displayError);
	}
	else
		displayError("ERROR: Passwords don't match");
}

function createDB()
{
	res = confirm("This process takes some time based on how much music you have, network speed, etc. Please be patient.");
	if (res)
	{
		document.location = "./index.php?p=createDB";
	}
}