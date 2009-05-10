function setComm()
{
	comm = $("#sngComm").val();
	alb_id = getOptions($("#albList")[0]);
	
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
	getArt(results["art"]);
	artOnChange(results["alb"]);
	albOnChange(results["sng"]);
}

function queryDB(query)
{
	if (query == "")
	{
		_getArt();
		$("#albList")[0].options.length = 0;
		$("#songList")[0].options.length = 0;
		return;
	}
	postData = "a=search&q=" + query + "&SID=" + SID;
	$.post("./ls.php", postData, procSearchResults, "json");
}

var timerID;

function search(query, reschedule)
{
	if (reschedule)
	{
		//alert(query);
		clearTimeout(timerID);
		timerID = setTimeout("search('" + query + "', false)", 1000);
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

function albOnChange(sngArr)
{
	$("#songList")[0].options.length = 0;
	for (i = 0; i < sngArr.length; i++)
	{
		$("#songList").append("<option value='["+ sngArr[i].id + ", \"" + sngArr[i].comm + "\"]'>"+ sngArr[i].name +"</option>");	
	}			
}

function _albOnChange(objSelect)
{
	alb_id = getOptions(objSelect);
	postData = "a=sng&alb=" + alb_id + "&SID=" + SID;
	$.post("./ls.php", postData, albOnChange, "json");
}

function artOnChange(albArr)
{
	$("#albList")[0].options.length = 0;
	$("#songList")[0].options.length = 0;
	//alert(albArr[0].id);
	for (i = 0; i < albArr.length; i++)
	{
		$("#albList").append("<option value='"+ albArr[i].id +"'>"+ albArr[i].name +"</option>");	
	}
}

function _artOnChange(objSelect)
{
	art_id = getOptions(objSelect);
	postData = "a=alb&artist=" + art_id + "&SID=" + SID;
	//alert(postData);
	$.post("./ls.php", postData, artOnChange, "json");
}

function getArt(artArr)
{
	$("#artList")[0].options.length = 0;
	for (i = 0; i < artArr.length; i++)
	{
		$("#artList").append("<option value='"+ artArr[i].id +"'>"+ artArr[i].name +"</option>");	
	}
}

function _getArt()
{
	postData = "a=art&SID=" + SID;
	$.post("./ls.php", postData, getArt, "json");
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
	sng = getOptions($("#songList")[0]);
	
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

function makeNewPlaylist(data)
{
	$("#errorDiv").addClass("info").text(data);
}

function _makeNewPlaylist()
{
	sng = getOptions($("#songList")[0]);
	if (sng == "") 
	{
		alert("The song list is empty, please select an album first.");
		exit();
	}			
	plName = prompt("Enter new playlist name: ", "New Playlist");
	if (plName != null)
	{
		postData = "a=cpl&sng=" + sng + "&pl=" + plName + "&SID=" + SID;
		$.post("./ls.php", postData, makeNewPlaylist);
	}
}

function addToPlaylist()
{
	$('#dialog').dialog('open');
}

$(document).ready(function(){
	postData = 'a=gett&SID=' + SID;
	$.post('./ls.php', postData, putTotals, 'json');
	_getArt();
	
	$("#dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 300,
		modal: true,
		buttons: {
			'Add to playlist': function() 
			{
				$(this).dialog('close');
			},
			'Cancel': function() 
			{
				$(this).dialog('close');
			}
		},
		close: function() 
		{
			allFields.removeClass('ui-state-error');
		}
	});
});