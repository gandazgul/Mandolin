$(document).ready(function(){
	_getSavedPL();
});

function getSavedPL(savedPLArr)
{
	$("#plList")[0].options.length = 0;
	for (i = 0; i < savedPLArr.length; i++)
	{
		$("#plList").append("<option value='" + savedPLArr[i] + "'>" + savedPLArr[i] + "</option>");	
	}
}

function _getSavedPL()
{
	postData = "a=saved&un=<?php if(isset($_SESSION["username"])) echo $_SESSION["username"]; ?>&SID=" + SID;
	$.post("./ls.php", postData, getSavedPL, "json");
}

function plOnChange(plContArr)
{
	$("#plContents")[0].options.length = 0;

	for (i = 0; i < plContArr.length; i++)
	{
		$("#plContents").append("<option value='"+ plContArr[i].id +"'>"+ plContArr[i].name +"</option>");	
	}
}

function _plOnChange(objSelect)
{
	pl_name = getOptions(objSelect);
	//alert(pl_name);
	postData = "a=retrPL&un=<?php if(isset($_SESSION["username"])) echo $_SESSION["username"]; ?>&pl=" + pl_name + "&SID=" + SID;
	//alert(postData);
	$.post("./ls.php", postData, plOnChange, "json");
}

			