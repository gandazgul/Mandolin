$(document).ready(function(){
	$("#accordion").accordion({autoHeight: false, collapsible: true});
});

function changePassw()
{
	if ( ($("#reNewPassw").val() == "" ) || ( $("#newPassw").val() == "" ) || ( $("#oldPassw").val() == "" ) )
	   $("#userMsg").addClass("message").html("ERROR: Passwords can't be empty");
	else   
    if ($("reNewPassw").value == $("newPassw").value)
	{
		postData = "a=cpassw&np=" + $("#newPassw").val() + "&op=" + $("#oldPassw").val();
		$.post("./dbuadm.php", postData, displayError);
	}
	else
		$("#userMsg").addClass("message").html("ERROR: Passwords don't match");
}
/*
function addUser(strUser, strPassword, rePassw)
{
	adminLvl = 2;
	if ($("#adminLvl")[0].checked)
		adminLvl = 0;
	else
	if ($("mantLvl").checked)
		adminLvl = 1;	
	if ( (strPassword == "") || (rePassw == "") )
		return "ERROR: Passwords can't be empty";
	else	
	if ( strPassword == rePassw )
	{
		ajaxpage("./exec.php?k=<?php echo $cur_key; ?>&action=addNewUser&user=" + strUser + "&passw=" + strPassword + "&adminLvl=" + adminLvl, "newUserResults");
	}
	else
		return "ERROR: Passwords don't match";	
}

/*
function createDB()
{
	res = confirm("Are you sure you want to recreate the database? The saved playlists will probably become invalid, and they can't be fixed.");
	if (res)
	{
		document.location = "./index.php?p=createDB";
	}
	else
		$("result").innerHTML = "Action cancelled";
}

function updateDB()
{
    document.location = "./index.php?p=updateDB";
}
*/