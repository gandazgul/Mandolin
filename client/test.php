<script type="text/javascript">
	$(document).ready(function(){
		postData = "a=allsongs&SID=" + SID;
		$.getJSON("./server/music.php", postData, displaySongs);		
	});

	function displaySongs(result)
	{
		var $tr = $("<tr>");
		var $td = $("<td>");
		for (var i = 0; i < result.data.length; i++)
		{
			$tr.clone().append($td.clone().text(result.data[i].song_name)).append($td.clone().text(result.data[i].alb_name)).append($td.clone().text(result.data[i].art_name)).appendTo("#insertHere");
		}
	}
</script>
<table>
	<thead>
		<th>Title</th><th>Album</th><th>Artist</th>
	</thead>
	<tbody id="insertHere">

	</tbody>
</table>