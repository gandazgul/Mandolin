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

	jQuery.fn.getSelectedItemID = function(){
		var selected = $(this).find(".ui-selected");
		var result = false;
		if (selected.length != 0)
			result = selected[0].id;

		return result;
	};

	jQuery.fn.getAllSelectedItems = function(){
		var result = "";
		$(this).find(".ui-selected").each(function(i, objItem){
			result += this.id + "|";
		});
		return result;
	};
});

function getAllSelected(list)
{
	
}

function displaySavedPL(savedPLArr)
{
	$("#plList").html('');
	$("#plContents").html('');
	for (var i = 0; i < savedPLArr.length; i++)
	{		
		$("#plList").append("<li class='ui-widget-content' id='"+ savedPLArr[i] +"'>"+ savedPLArr[i] +"</li>");
	}

	$("#plList li").contextMenu({menu: 'plMenu'},
		function(action, el, pos)
		{
			var plName = "";
			var postData = "";
			var r = false;
			switch (action)
			{
				case "playrand":
				case "play": {
					plName = $("#plList").getAllSelectedItems();
					//alert(plName);
					if (plName == "")
					{
						displayError("You must select a playlist before clicking Play.");
					}
					else
					{
						if (action == "playrand")
							$("#rnd").val("true");
						else
							$("#rnd").val("false");
						$("#pl_or_sng").attr('name', 'pl').val(plName);
						$("#SID").val(SID);
						$("#downForm").get(0).submit();
					}
					break;
				}
				case "rename": {
					plName = $("#plList").getSelectedItemID();
					//alert(plName);
					if (!plName)
					{
						displayError("You must select a playlist.");
					}
					else
					{
						var plNewName = prompt("Enter a new name for \"" + plName + "\"", plName);
						if ((plNewName == null) || (plNewName == "")) return;
						postData = "a=ren&pl=" + escape(plName) + "&npl=" + escape(trim(plNewName)) + "&SID=" + SID;
						$.post("./server/pl.php", postData, displaySavedPL, 'json');
					}
					break;
				}
				case "delete": {
					plName = $("#plList").getSelectedItemID();
					//alert(plName);
					if (!plName)
					{
						displayError("You must select a playlist.");
					}
					else
					{
						r = confirm("Are you sure you want to delete: " + plName);
						if (r)
						{
							postData = "a=del&pl=" + escape(plName) + "&SID=" + SID;
							$.post("./server/pl.php", postData, displaySavedPL, 'json');
						}
					}
					break;
				}
				case "shuffle": {
					plName = $("#plList").getSelectedItemID();
					//alert(plName);
					if (!plName)
					{
						displayError("You must select a playlist.");
					}
					else
					{
						r = confirm("Are you sure you want to shuffle: " + plName);
						if (r)
						{
							postData = "a=shuf&pl=" + plName + "&SID=" + SID;
							$.post("./server/pl.php", postData, displayPLContents, 'json');
						}
					}
					break;
				}
			}//switch

		}//function
	);
}

function getSavedPL()
{
	var postData = "a=saved&SID=" + SID;
	$.post("./server/pl.php", postData, displaySavedPL, "json");
}

function displayPLContents(plContArr)
{
	$("#plContents").html('');
	for (var i = 0; i < plContArr.length; i++)
	{
		$("#plContents").append("<li class='ui-widget-content' id='"+ plContArr[i].id +"'>"+ plContArr[i].name +"</li>");
	}

	jQuery.fn.getAllItems = function(){
		var result = "";
		$(this).find("li").each(function(i, objOption){
			result += objOption.id + "|";
		});
		return result;
	}

	$("#plContents li").contextMenu({menu: 'songsMenu'},
		function(action, el, pos)
		{
			var postData = "";
			var songID = "";
			switch (action)
			{
				case "playrand":
				case "play": {
					songID = $("#plContents").getAllSelectedItems();
					//alert(songID);
					if (songID == "")
					{
						displayError("You must select a playlist before clicking Play.");
					}
					else
					{
						if (action == "playrand")
							$("#rnd").val("true");
						else
							$("#rnd").val("false");
						$("#pl_or_sng").attr('name', 'sng').val(songID);
						$("#SID").val(SID);
						$("#downForm").get(0).submit();
					}
					break;
				}
				case "delete": {
					var selected = $("#plContents .ui-selected");
					if (selected.length == 0)
					{
						displayError("There is nothing selected.");
					}
					else
					{
						selected.remove();
						plName = $("#plList").getSelectedItemID();
						songID = $("#plContents").getAllItems();
						postData = "a=updPL&name=" + plName + "&newC=" + songID + "&concat=false&SID=" + SID;
						alert(postData);
						$.post("./server/pl.php", postData);						
					}
					break;
				}
				case "moveup": {
					
					break;
				}
				case "moveup": {

					break;
				}
			}//switch

		}//function
	);
}

function getPLContents(plList)
{
	$("#plContents").append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
	var postData = "a=retrPL&pl=" + plList + "&SID=" + SID;
	//alert(postData);
	$.post("./server/pl.php", postData, displayPLContents, "json");
}

function delFromPl()
{

}

function move(up)
{
	var objSelect = $("#plContents")[0];
	var x = objSelect.selectedIndex;
	var tmpTxt = "";
	var tmpVal = "";
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
	
	var sng_id = getAllOptions(objSelect);
	//alert(sng_id);
	var plSelect = $("#plList")[0];
	var pl_name = plSelect.options[plSelect.selectedIndex].value
	var postData = "a=updPL&name=" + pl_name + "&newC=" + sng_id + "&concat=false&SID=" + SID;
	$.post("./server/pl.php", postData);
}
