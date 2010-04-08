$(document).ready(function(){
	$.getJSON('./server/music.php', 'a=gett&SID=' + SID, displayTotals);
	getArtists();

	$("#artistsList").selectable({
		stop: function(){
			var artIDList = "";
			$(".ui-selected", this).each(function(){
				//var index = $("#artistsList li").index(this);
				artIDList += this.id + "|";
			});
			//alert(strResult);
			$("#albumList").empty().append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
			postData = "a=albums&artist_id=" + artIDList + "&SID=" + SID;
			$.getJSON("./server/music.php", postData, displayAlbums);
		}
	});

	$("#albumList").selectable({
		stop: function(){
			var albIDList = "";
			$(".ui-selected", this).each(function(){
				//var index = $("#artistsList li").index(this);
				albIDList += this.id + "|";
			});
			//alert(strResult);
			$("#songList").empty().append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
			postData = "a=songs&album_id=" + albIDList + "&SID=" + SID;
			$.getJSON("./server/music.php", postData, displaySongs);
		}
	});

	$("#songList").selectable({
		/*stop: function(){
			var strResult = "";
			//alert(this.id);
			$(".ui-selected", this).each(function(){
				//var index = $("#artistsList li").index(this);
				var alb_id = this.id;
				strResult += alb_id + "|";
			});
			alert(strResult);
			getSongs(strResult);
		}*/
	});

	$("#addToPLDiag").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 165,
		modal: true,
		buttons: {
			'Add to playlist': function()
			{
				var plID = $("#tmpPlList").val();
				var data = new function(){
					this.pl_contents = "'" + $("#songList").getAllSelectedItems() + "'";
				};
				var postData = "a=put&id=" + plID + "&data=" + escape(JSON.stringify(data)) + "&concat=true&SID=" + SID;
				$.get("./server/playlists.php", postData, addToPLResponse, 'json');
				$(this).dialog('close');
			},
			'Cancel': function()
			{
				$(this).dialog('close');
			}
		}
	});

	jQuery.print = function(message, insertionType)
	{
		if (typeof(message) == 'object')
		{
			var string = '{<br />',
			values = [],
			counter = 0;
			$.each(message, function(key, value)
			{
				if (value && value.nodeName)
				{
					var domnode = '&lt;' + value.nodeName.toLowerCase();
					domnode += value.className ? ' class="' + value.className + '"' : '';
					 domnode += value.id ? ' id="' + value.id + '"' : '';
					 domnode += '&gt;';
					 value = domnode;
				}
				values[counter++] = key + ': ' + value;
			});

			string += values.join(',<br />');
			string += '<br />}';
			message = string;
		 }

		 var $output = $('#print-output');

		 if ($output.length === 0) 
		 {
			$output = $('<div id="print-output" />').appendTo('body');
		 }

		 var $newMessage = $('<div class="print-output-line" />');
		 $newMessage.html(message);
		 insertionType = insertionType || 'append';
		 $output[insertionType]($newMessage);
	};

	$("#searchBox").keyup(function(e){
		if (e.keyCode != 17)
		{
			search($(this).val(), true);
		}
	});
});

function displayTotals(data)
{
	if (data.isError)
	{}
	else
	{
		$("#artTotal").html(data.resultStr[0]);
		/*$("#albTotal").html(data[1]);
		$("#sngTotal").html(data[2]);*/
	}
}

function getArtists()
{
	postData = "a=artists&SID=" + SID;
	$.get("./server/music.php", postData, displayArtists, "json");
}

function addToPLResponse(data)
{
	if (data.length == 0)
	{
		displayError('There was an error adding the selected songs to the playlist.');
	}
	else
	{
		displayError('The songs were added successfully.');
	}
}

function procSearchResults(results)
{
	if (results.isError)
	{
		displayError(results.errorStr);
	}
	else
	{
		displayArtists(results.data.art);
		displayAlbums(results.data.alb);
		displaySongs(results.data.sng);
	}
}

function queryDB(query)
{
	//alert(query);
	if (query == "")
	{
		getArtists();
		$("#albumList").html('');
		$("#songList").html('');
		return;
	}
	postData = "a=search&q=" + query + "&SID=" + SID;
	$.getJSON("./server/music.php", postData, procSearchResults);
}

var srchTimerID;

function search(query, reschedule)
{
	//TODO Exclude ctrl from triggering this function
	clearTimeout(srchTimerID);
	if (reschedule)
	{
		//alert(query);
		srchTimerID = setTimeout("search('" + query + "', false)", 1000);
	}
	else
	{
		queryDB(query);		
	}	
}

function displayAddToPLDiag(savedPLArr)
{
	$("#tmpPlList")[0].options.length = 0;
	for (i = 0; i < savedPLArr.length; i++)
	{
		$("#tmpPlList").append("<option value='" + savedPLArr[i].id + "'>" + savedPLArr[i].name + "</option>");
	}
	$("#addToPLDiag").dialog('open');
}

function displaySongs(songs)
{
	if (songs.isError)
	{
		displayError(songs.errorStr);
	}
	else
	{
		$("#songList").html('');
		for (i = 0; i < songs.data.length; i++)
		{
			$("#songList").append("<li class='ui-widget-content' id='" + songs.data[i].id + "'>"+ songs.data[i].name +"</li>");
		}

		$("#songList li").contextMenu(
			{menu: 'songsMenu'},
			function(action, el, pos)
			{
				sngIDList = $("#songList").getAllSelectedItems();

				switch (action)
				{
					case "playrand":
					case "play": {
						//alert(sngIDs);
						if (sngIDList == "")
						{
							displayError("You must select some tracks before clicking Play. Try Select All, then Play.");
						}
						else
						{
							if (action == "playrand")
								$("#rnd").val("true");
							else
								$("#rnd").val("false");
							$("#sng").val(sngIDList);
							$("#SID").val(SID);
							$("#playForm").get(0).submit();
						}
						break;
					}
					case "selectall": {
						$("#songList").children().addClass("ui-selected");
						break;
					}
					case "createpl": {
						if (sngIDList == "")
						{
							alert("You must select some tracks to add to the new playlist");
						}
						else
						{
							var plName = trim(prompt("Enter new playlist name: ", "New Playlist"));
							//alert(plName);
							if (plName != null)
							{
								postData = "a=playlists&pl_contents=" + sngIDList + "&pl_name=" + escape(plName) + "&SID=" + SID;
								$.post("./server/playlists.php", postData, displayError);
							}
						}
						break;
					}
					case "addtopl": {
						postData = "a=playlists&SID=" + SID;
						$.get("./server/playlists.php", postData, displayAddToPLDiag, "json");
						break;
					}
				}//switch

			}//function
		);
		$('#artnAlbMenu').disableContextMenuItems('#rename,#delete');
	}
}//displaySongs

function displayAlbums(albums)
{
	if (albums.isError)
	{
		displayError(albums.errorStr);
	}
	else
	{
		$("#albumList").html('');
		$("#songList").html('');
		//alert(albArr[0].id);
		for (i = 0; i < albums.data.length; i++)
		{
			$("#albumList").append("<li class='ui-widget-content' id='"+ albums.data[i].id +"'>"+ albums.data[i].name +"</li>");
		}

		$("#albumList li").contextMenu({
			menu: 'artnAlbMenu'
		}, function(action, el, pos) {
			switch (action)
			{
				case "play":
				case "playrand":
				{
					alert("TODO: Implement this. Album: " + $(el).attr('id'));
					break;
				}
			}
		});
	}
}

function displayArtists(artArr)
{
	$("#artistsList").html('');
	for (i = 0; i < artArr.length; i++)
	{
		$("#artistsList").append("<li class='ui-widget-content' id='"+ artArr[i].id +"'>"+ artArr[i].name +"</li>");
	}
	
	// Show menu when a list item is clicked
	$("#artistsList li").contextMenu({
		menu: 'artnAlbMenu'
	}, function(action, el, pos) {
		switch (action)
		{
			case "play":
			case "playrand":
			{ 
				alert("TODO: Implement this. Artist: " + $(el).attr('id')); 
				
				break; 
			}
		}
	});
	$('#artnAlbMenu').disableContextMenuItems('#rename,#delete');
}

/*function sngOnChange(sng_value)
{
	$("#sngComm").val("");
	$("#sngID").html("");
	data = eval('('+ sng_value +')');
	//alert(data[1]);
	if (data[1] != null)
	{
		$("#sngComm").val(data[1]);
	}
	$("#sngID").val(data[0]);
}

function setComm()
{
	comm = $("#sngComm").val();
	alb_id = getSelectedOptions($("#albList")[0]);

	postData =  "a=addc";
	postData += "&com=" + comm;
	postData += "&sng=" + $("#sngID").val();
	postData += "&alb=" + alb_id;
	postData += "&SID=" + SID;
	//alert(postData);
	$.post("./server/music.php", postData, albOnChange, "json");
}*/