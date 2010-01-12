$(document).ready(function(){
	getSavedPL();
	
	$("#plList").selectable({
		stop: function(){
			var strResult = "";
			//alert(this.id);
			$(".ui-selected", this).each(function(){
				//var index = $("#artistsList li").index(this);
				var pl_name = this.id;
				strResult += pl_name + "|";
			});
			//alert(strResult);
			getPLContents(strResult);
		}
	});

	$("#plContents").selectable();
});

function displaySavedPL(savedPLArr)
{
	$("#plList").html('');
	$("#plContents").html('');
	for (i = 0; i < savedPLArr.length; i++)
	{		
		$("#plList").append("<li class='ui-widget-content' id='"+ savedPLArr[i] +"'>"+ savedPLArr[i] +"</li>");
	}
}

function getSavedPL()
{
	postData = "a=saved&SID=" + SID;
	$.post("./server/pl.php", postData, displaySavedPL, "json");
}

function displayPLContents(plContArr)
{
	$("#plContents").html('');
	for (i = 0; i < plContArr.length; i++)
	{
		$("#plContents").append("<li class='ui-widget-content' id='"+ plContArr[i].id +"'>"+ plContArr[i].name +"</li>");
	}
}

function getPLContents(plList)
{
	$("#plContents").append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
	postData = "a=retrPL&pl=" + plList + "&SID=" + SID;
	//alert(postData);
	$.post("./server/pl.php", postData, displayPLContents, "json");
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
		$("#SID").val(SID);
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
		$.post("./server/pl.php", postData, displaySavedPL, 'json');
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
		$.post("./server/pl.php", postData, displaySavedPL, 'json');
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
		$.post("./server/pl.php", postData, _plOnChange, 'json');
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
	$.post("./server/pl.php", postData);
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
	$.post("./server/pl.php", postData);
}
