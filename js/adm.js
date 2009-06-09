$(document).ready(function(){
	$("#accordion").accordion({autoHeight: false, collapsible: true, active: false});
});

function changePassw()
{
	if ( ($("#reNewPassw").val() == "" ) || ( $("#newPassw").val() == "" ) || ( $("#oldPassw").val() == "" ) )
	   displayError("ERROR: Passwords can't be empty");
	else
    if ($("reNewPassw").value == $("newPassw").value)
	{
		postData = "a=cpassw&np=" + $("#newPassw").val() + "&op=" + $("#oldPassw").val();
		$.post("./dbuadm.php", postData, displayError);
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
		$.post("./dbuadm.php", postData, displayError);
	}
	else
		displayError("ERROR: Passwords don't match");
}

function createDB()
{
	res = confirm("Are you sure you want to recreate the database? The saved playlists will probably become invalid, and they can't be fixed.");
	if (res)
	{
		document.location = "./index.php?p=createDB";
	}
}

function updateDB()
{
    document.location = "./index.php?p=updateDB";
}
