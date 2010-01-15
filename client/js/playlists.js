$(document).ready(function(){
	var postData = "a=playlists&SID=" + SID;
	$.post("./server/playlists.php", postData, displaySavedPL, "json");

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

	$("#plList").selectable({
		stop: function(){
			var plList = $(this).getAllSelectedItems();

			$("#plContents").append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
			postData = "a=playlists&id=" + plList + "&SID=" + SID;
			//alert(postData);
			$.post("./server/playlists.php", postData, displayPLContents, "json");

			//alert(this.id);
			/*$(".ui-selected", this).each(function(){
				//var index = $("#artistsList li").index(this);
				var pl_name = this.id;
				strResult += pl_name + "|";
			});*/
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
	for (var i = 0; i < savedPLArr.length; i++)
	{		
		$("#plList").append("<li class='ui-widget-content' id='"+ savedPLArr[i].id +"'>"+ savedPLArr[i].name +"</li>");
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
						$.post("./server/playlists.php", postData, displaySavedPL, 'json');
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
							$.post("./server/playlists.php", postData, displaySavedPL, 'json');
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
							$.post("./server/playlists.php", postData, displayPLContents, 'json');
						}
					}
					break;
				}
			}//switch

		}//function
	);
}

function displayPLContents(plContArr)
{
	$("#plContents").html('');
	if (plContArr['isError'])
		displayError(plContArr['resultStr'])
	else
	{
		for (var i = 0; i < plContArr['resultStr'].length; i++)
		{
			$("#plContents").append("<li class='ui-widget-content' id='"+ plContArr['resultStr'][i][0]['song_id'] +"'>"+ plContArr['resultStr'][i][0]['song_name'] +"</li>");
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
				var plName = "";
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
							plName = $("#plList").getSelectedItemID();
							var r = confirm("Are you sure you want to delete this songs from: " + plName);
							if (r)
							{
								selected.remove();
								songID = $("#plContents").getAllItems();
								postData = "a=updPL&name=" + plName + "&newC=" + songID + "&concat=false&SID=" + SID;
								//alert(postData);
								$.post("./server/playlists.php", postData);
							}
						}
						break;
					}
					case "selectall": {
						$("#plContents").children().addClass("ui-selected");
						break;
					}
					case "moveup":
					case "movedown": {
						if (action == "moveup")
							$("#plContents .ui-selected").insertBefore($("#plContents .ui-selected:first").prev());
						else
							$("#plContents .ui-selected").insertAfter($("#plContents .ui-selected:last").next());

						plName = $("#plList").getSelectedItemID();
						songID = $("#plContents").getAllItems();
						postData = "a=updPL&name=" + plName + "&newC=" + songID + "&concat=false&SID=" + SID;
						//alert(postData);
						$.post("./server/playlists.php", postData);
						break;
					}
				}//switch

			}//function
		);
	}
}
