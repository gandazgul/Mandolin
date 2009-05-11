$(document).ready(function(){
	postData = "a=saved&un=<?php if(isset($_SESSION["username"])) echo $_SESSION["username"]; ?>&SID=" + SID;
	$.post("./ls.php", postData, getSavedPL, "json");
});

function getSavedPL(savedPLArr)
{
	$("#plList")[0].options.length = 0;
	for (i = 0; i < savedPLArr.length; i++)
	{
		$("#plList").append("<option value='" + savedPLArr[i] + "'>" + savedPLArr[i] + "</option>");	
	}
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

function ranPlayPL()
{
	$("#rnd").val("true");
	playPL();
}

function playPL()
{
	plSelect = $("#plList")[0];
	
	if (plSelect.selectedIndex == -1)
	{
		alert("Please select a list first.");
		return;
	}
	else
	{
		pl_name = plSelect.options[plSelect.selectedIndex].value;
		//alert(pl_name);
		$("#pl").val(pl_name);
		$("#downForm")[0].submit();
		$("#rnd").val("false");
	}
}

function delPL()
{
	plSelect = $("#plList")[0];
	
	if (plSelect.selectedIndex == -1)
	{
		alert("Please select a list first.");
		return;
	}
	else
	{
		pl_name = plSelect.options[plSelect.selectedIndex].value;
		//alert(pl_name);
		postData = "a=del&pl=" + pl_name + "&SID=" + SID;
		$.post("./ls.php", postData, getSavedPL, 'json');
	}
}

function shuffle()
{
	plSelect = $("#plList")[0];
	
	if (plSelect.selectedIndex == -1)
	{
		alert("Please select a list first.");
		return;
	}
	else
	{
		pl_name = plSelect.options[plSelect.selectedIndex].value;
		//alert(pl_name);
		postData = "a=shuf&pl=" + pl_name + "&SID=" + SID;
		$.post("./ls.php", postData, plOnChange, 'json');
	}
}

function delFromPl()
{
	objSelect = $("#plContents")[0];
	if (objSelect.options.selectedIndex == -1) return;
	while (objSelect.options.selectedIndex != -1)
	{
		objSelect.options[objSelect.options.selectedIndex] = null;
	}
	sng_id = getOptions(objSelect);
	plSelect = $("#plList")[0];
	pl_name = plSelect.options[plSelect.selectedIndex].value
	postData = "a=updPL&name=" + pl_name + "&newC=" + sng_id;
	$.post("./ls.php", postData, displayError);
}
