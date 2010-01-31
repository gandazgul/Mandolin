function procSearchResults(results)
{
	displayMovies(results["art"]);
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
	$.post("./server/movies.php", postData, procSearchResults, "json");
}

var srchTimerID;

function search(query, reschedule)
{
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

function embedTheMovie(movieTAG)
{
	//alert(movieTAG);
	$("#movieContainer").html(movieTAG);
}

function displayMovies(movArr)
{
	//alert(movArr);
	$("#moviesList").html('');
	for (i = 0; i < movArr.length; i++)
	{
		$("#moviesList").append("<li class='ui-widget-content'>-= " + movArr[i][0] + " =-</li>");
		for (j = 1; j < movArr[i].length; j++)
		{
			$("#moviesList").append("<li class='ui-widget-content' id='" + movArr[i][j].id + "'>" + movArr[i][j].title + "</li>");
		}
	}
	
	// Show menu when a list item is clicked
	$("#moviesList li").contextMenu({
		menu: 'moviesMenu'
	}, function(action, el, pos) {
		switch (action)
		{
			case "changecat":
			case "delete":
			case "rename":
			{
				alert("TODO");
				break;
			}
		}
	});
	$('#moviesMenu').disableContextMenuItems('#rename,#delete');
	
	$("#moviesList").children().each(function(){
		$(this).click(function(){
			$(this).parent().children().removeClass("ui-selected");
			$(this).addClass("ui-selected");
			
			$("#movieContainer").append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
			postData = "a=playmov&id=" + $(this).get(0).id + "&SID=" + SID;
			$.post("./server/movies.php", postData, embedTheMovie);			
		});
	});
}

function getMovies()
{
	$("#moviesList").append("<li class='ui-widget-content'><img alt='Loading...' src='./client/images/ajax-loader.gif' /></li>");
	postData = "a=mov&SID=" + SID;
	$.post("./server/movies.php", postData, displayMovies, "json");
}

$(document).ready(function(){
	getMovies();

	/*$("#moviesList").selectable({
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
	});*/
});
