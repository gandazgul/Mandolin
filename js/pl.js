$(document).ready(function(){
	postData = "a=saved&SID=" + SID;
	$.post("./ls.php", postData, displaySavedPL, "json");
});

function displaySavedPL(savedPLArr)
{
	$("#plList")[0].options.length = 0;
	$("#plContents")[0].options.length = 0;
	for (i = 0; i < savedPLArr.length; i++)
	{
		$("#plList").append("<option value=\"" + savedPLArr[i] + "\">" + savedPLArr[i] + "</option>");	
	}
}

function _plOnChange(plContArr)
{
	$("#plContents")[0].options.length = 0;
	for (i = 0; i < plContArr.length; i++)
	{
		$("#plContents").append("<option value='"+ plContArr[i].id +"'>"+ plContArr[i].name +"</option>");	
	}
}

function plOnChange()
{
	plSelect = $("#plList")[0];
	pl_name = getSelectedOptions(plSelect);
	//alert(pl_name);
	postData = "a=retrPL&pl=" + pl_name + "&SID=" + SID;
	//alert(postData);
	$.post("./ls.php", postData, _plOnChange, "json");
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

function renPL()
{
	plSelect = $("#plList")[0];
	
	if (plSelect.selectedIndex == -1)//no list selected
		return;
	else
	{
		plName = plSelect.options[plSelect.selectedIndex].value;
		plNewName = prompt("Enter a new name for \"" + plName + "\"", plName);
		if ((plNewName == null) || (plNewName == "")) return;
		//alert(plName + " " + plNewName);
		postData = "a=ren&pl=" + escape(plName) + "&npl=" + escape(trim(plNewName)) + "&SID=" + SID;
		$.post("./ls.php", postData, displaySavedPL, 'json');
	}
}

function delPL()
{
	plSelect = $("#plList")[0];
	
	if (plSelect.selectedIndex == -1)//no list selected
		 return;
	else
	{
		pl_name = plSelect.options[plSelect.selectedIndex].value;
		//alert(pl_name);
		postData = "a=del&pl=" + escape(pl_name) + "&SID=" + SID;
		$.post("./ls.php", postData, displaySavedPL, 'json');
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
		$.post("./ls.php", postData, _plOnChange, 'json');
	}
}

function getSelectedOptions(objSelect)//get all selected options in a <select> and separate them with |
{
	txt = "";
	for (i = 0; i < objSelect.options.length; i++)
	{
		if (objSelect.options[i].selected)
		{
			txt = txt + escape(objSelect.options[i].value) + "|";
		}
	}
	return txt;
}

function getAllOptions(objSelect)//get all selected options in a <select> and separate them with |
{
	txt = "";
	for (i = 0; i < objSelect.options.length; i++)
	{
		value = objSelect.options[i].value;
		txt = txt + escape(value) + "|";
	}
	return txt;
}

function delFromPl()
{
	objSelect = $("#plContents")[0];
	if (objSelect.options.selectedIndex == -1) return;
	while (objSelect.options.selectedIndex != -1)
	{
		objSelect.options[objSelect.options.selectedIndex] = null;
	}
	sng_id = getAllOptions(objSelect);
	//alert(sng_id);
	
	plSelect = $("#plList")[0]; 
	pl_name = plSelect.options[plSelect.selectedIndex].value
	postData = "a=updPL&name=" + pl_name + "&newC=" + sng_id + "&concat=false&SID=" + SID;
	$.post("./ls.php", postData);
}

function move(up)
{
	objSelect = $("#plContents")[0];
	x = objSelect.selectedIndex;
	if (objSelect.options.selectedIndex == -1) return;
	if (up)
	{
		tmpTxt = objSelect.options[x-1].text;
		tmpVal = objSelect.options[x-1].value;
		objSelect.options[x-1].text = objSelect.options[x].text;
		objSelect.options[x-1].value = objSelect.options[x].value;
		objSelect.options[x].text = tmpTxt;
		objSelect.options[x].value = tmpVal;	
		objSelect.options[x-1].selected = true;
		objSelect.options[x].selected = false;
	}
	else
	{
		tmpTxt = objSelect.options[x+1].text;
		tmpVal = objSelect.options[x+1].value;
		objSelect.options[x+1].text = objSelect.options[x].text;
		objSelect.options[x+1].value = objSelect.options[x].value;
		objSelect.options[x].text = tmpTxt;
		objSelect.options[x].value = tmpVal;	
		objSelect.options[x+1].selected = true;
		objSelect.options[x].selected = false;
	}
	
	sng_id = getAllOptions(objSelect);
	//alert(sng_id);
	plSelect = $("#plList")[0];
	pl_name = plSelect.options[plSelect.selectedIndex].value
	postData = "a=updPL&name=" + pl_name + "&newC=" + sng_id + "&concat=false&SID=" + SID;
	$.post("./ls.php", postData);
}
