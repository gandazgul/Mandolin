$(document).ready(function(){
	var postData = "a=playlists&SID=" + SID;
	$.get("./server/playlists.php", postData, displaySavedPL, "json");

	$("#plList").selectable({
		stop: function(){
			var plList = $(this).getAllSelectedItems();
			$("#plContents").append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
			postData = "a=playlists&id=" + plList + "&SID=" + SID;
			//alert(postData);
			$.get("./server/playlists.php", postData, displayPLContents, "json");
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
			var plName = $("#plList").getSelectedItemName();
			var plID = $("#plList").getSelectedItemID();
			var postData = "";
			var r = false;
			switch (action)
			{
				case "playrand":
				case "play": {
					var plIDList = $("#plList").getAllSelectedItems();
					//alert(plName);
					if (plIDList == "")
					{
						displayError("You must select a playlist before clicking Play.");
					}
					else
					{
						if (action == "playrand")
							$("#rnd").val("true");
						else
							$("#rnd").val("false");
						$("#pl_or_sng").attr('name', 'pl').val(plIDList);
						$("#SID").val(SID);
						$("#downForm").get(0).submit();
					}
					break;
				}
				case "rename": {
					if (!plName)
					{
						displayError("You must select a playlist.");
					}
					else
					{
						var plNewName = prompt("Enter a new name for \"" + plName + "\"", plName);
						if ((plNewName == null) || (plNewName == "")) return;
						var data = new function(){
							this.pl_name = "'" + plNewName + "'";
						};
						postData = "a=put&data=" + escape(JSON.stringify(data)) + "&id=" + plID + "&SID=" + SID;
						//alert(postData);
						$.get("./server/playlists.php", postData, displaySavedPL, 'json');
					}
					break;
				}
				case "delete": {
					if (!plName)
					{
						displayError("You must select a playlist.");
					}
					else
					{
						r = confirm("Are you sure you want to delete: '"+ plName +"'?");
						if (r)
						{
							postData = "a=delete&id=" + escape(plID) + "&SID=" + SID;
							$.get("./server/playlists.php", postData, displaySavedPL, 'json');
						}
					}
					break;
				}
				case "shuffle": {
					if (!plName)
					{
						displayError("You must select a playlist.");
					}
					else
					{
						r = confirm("Are you sure you want to shuffle: " + plName);
						if (r)
						{
							postData = "a=shuf&id=" + plID + "&SID=" + SID;
							$.get("./server/playlists.php", postData, displayPLContents, 'json');
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
				var songIDList = $("#plContents").getAllSelectedItems();
				var plName = "";
				CData = function(contents){
					this.pl_contents = "'" + contents + "'";
				};
				switch (action)
				{
					case "playrand":
					case "play": {
						//alert(songID);
						if (songIDList == "")
						{
							displayError("You must select a playlist before clicking Play.");
						}
						else
						{
							if (action == "playrand")
								$("#rnd").val("true");
							else
								$("#rnd").val("false");
							$("#pl_or_sng").attr('name', 'sng').val(songIDList);
							$("#SID").val(SID);
							$("#downForm").get(0).submit();
						}
						break;
					}
					case "delete": {
						if (songIDList == "")
						{
							displayError("There is nothing selected.");
						}
						else
						{
							plName = $("#plList").getSelectedItemName();
							var r = confirm("Are you sure you want to delete this songs from: " + plName);
							if (r)
							{
								$("#plContents .ui-selected").remove();
								data = new CData($("#plContents").getAllItems());
								postData = "a=put&id=" + $("#plList").getSelectedItemID() + "&data=" + escape(JSON.stringify(data)) + "&SID=" + SID;
								//alert(postData);
								$.get("./server/playlists.php", postData);
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
						selected = $("#plContents .ui-selected");
						if (selected.length == 0)
						{
							displayError("There is nothing selected.");
						}
						else
						{
							if (action == "moveup")
								selected.insertBefore($("#plContents .ui-selected:first").prev());
							else
								selected.insertAfter($("#plContents .ui-selected:last").next());

							data = new CData($("#plContents").getAllItems());
							postData = "a=put&id=" + $("#plList").getSelectedItemID() + "&data=" + escape(JSON.stringify(data)) + "&SID=" + SID;
							//alert(postData);
							$.get("./server/playlists.php", postData);
						}
						break;
					}
				}//switch

			}//function
		);
	}
}
