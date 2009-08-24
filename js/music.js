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
	$.post("./ls.php", postData, albOnChange, "json");
}

function procSearchResults(results)
{
	displayArtists(results["art"]);
	displayAlbums(results["alb"]);
	displaySongs(results["sng"]);
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
	$.post("./ls.php", postData, procSearchResults, "json");
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

function sngOnChange(sng_value)
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

function displaySongs(sngArr)
{
	$("#songList").html('');
	for (i = 0; i < sngArr.length; i++)
	{
		$("#songList").append("<li class='ui-widget-content' id='["+ sngArr[i].id + ", \"" + sngArr[i].comm + "\"]'>"+ sngArr[i].name +"</li>");	
	}
	
	$("#songList li").contextMenu({
		menu: 'songsMenu'
	}, function(action, el, pos) {
		switch (action)
		{
			case "play":
			case "playrand": 
			{ 
				alert("TODO: Implement this. Song: " + $(el).attr('id')); 
				break; 
			}
		}
	});
	$('#artnAlbMenu').disableContextMenuItems('#rename,#delete');	
}

function getSongs(albIDs)
{
	postData = "a=sng&alb=" + albIDs + "&SID=" + SID;
	$.post("./ls.php", postData, displaySongs, "json");
}

function displayAlbums(albArr)
{
	$("#albumList").html('');
	$("#songList").html('');
	//alert(albArr[0].id);
	for (i = 0; i < albArr.length; i++)
	{
		$("#albumList").append("<li class='ui-widget-content' id='"+ albArr[i].id +"'>"+ albArr[i].name +"</li>");	
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
	//$('#artnAlbMenu').disableContextMenuItems('#rename,#delete');	
}

function getAlbums(artIDs)
{
	postData = "a=alb&artist=" + artIDs + "&SID=" + SID;
	//alert(postData);
	$.post("./ls.php", postData, displayAlbums, "json");
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

function getArtists()
{
	postData = "a=art&SID=" + SID;
	$.post("./ls.php", postData, displayArtists, "json");
}

function putTotals(data)
{
	$("#artTotal").html(data[0]);
	$("#albTotal").html(data[1]);
	$("#sngTotal").html(data[2]);
}

function selRandPlay()
{
	$("#rnd").val("true");
	selPlay();
}

function selPlay()
{
	sng = getSelectedOptions($("#songList")[0]);
	
	if (sng == "") 
	{
		alert("The song list is empty, please select an album first.");
	}
	else 
	{
		$("#sng").val(sng);
		$("#playForm")[0].submit();
		$("#rnd").val("false");
	}
}

function createPlaylist()
{
	sng = getSelectedOptions($("#songList")[0]);
	if (sng == "") 
	{
		alert("The song list is empty, please select an album first.");
		exit();
	}			
	plName = trim(prompt("Enter new playlist name: ", "New Playlist"));
	if (plName != null)
	{
		postData = "a=cpl&sng=" + sng + "&pl=" + plName + "&SID=" + SID;
		$.post("./ls.php", postData, displayError);
	}
}

function addToPlaylist(plArr)
{
	$("#tmpPlList")[0].options.length = 0;
	for (i = 0; i < plArr.length; i++)
	{
		$("#tmpPlList").append("<option value='" + plArr[i] + "'>" + plArr[i] + "</option>");	
	}
	$('#dialog').dialog('open');
}

function _addToPlaylist()
{
	if ($("#songList")[0].selectedIndex == -1)
	{
		alert("There are no songs selected, please make a selection first.");
		return;
	}
	postData = "a=saved&un=<?php if(isset($_SESSION["username"])) echo $_SESSION["username"]; ?>&SID=" + SID;
	$.post("./ls.php", postData, addToPlaylist, "json");
}

$(document).ready(function(){
	//postData = 'a=gett&SID=' + SID;
	//$.post('./ls.php', postData, putTotals, 'json');
	getArtists();

	$("#artistsList").selectable({
		stop: function(){
			var strResult = "";
			//alert(this.id);
			$(".ui-selected", this).each(function(){
				//var index = $("#artistsList li").index(this);
				var art_id = this.id;
				strResult += art_id + "|";
			});
			//alert(strResult);
			getAlbums(strResult);
		}
	});
	
	$("#albumList").selectable({
		/*stop: function(){
			var strResult = "";
			//alert(this.id);
			$(".ui-selected", this).each(function(){
				//var index = $("#artistsList li").index(this);
				var alb_id = this.id;
				strResult += alb_id + "|";
			});
			//alert(strResult);
			getSongs(strResult);
		}*/
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
	
	$("#dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 150,
		modal: true,
		buttons: {
			'Add to playlist': function() 
			{
				pl_name = $("#tmpPlList").val();
				postData = "a=adds&name=" + pl_name + "&pl=" + getSelectedOptions($("#songList")[0]) + "&SID=" + SID;
				$.post("./ls.php", postData, displayError);
				$(this).dialog('close');
			},
			'Cancel': function() 
			{
				$(this).dialog('close');
			}
		}
	});
});