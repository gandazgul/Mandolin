function trim(str)
{
	return str.replace(/^\s+|\s+$/g,"");
}

//callback function to bring a hidden box back
function hideError()
{
	setTimeout(function(){
		$("#errorDiv").css("height", "0px").css("padding", "0px").hide().text("");
	}, 10000);
}

function displayError(data, msgClass)
{
	if (msgClass == 'success') msgClass = "message";
	if (typeof msgClass == 'undefined') msgClass = 'error';

	$("#errorDiv").addClass(msgClass).text(data).show().animate({height: "15px", padding: "10px"}, 500, "linear", hideError);
}

function displayMessage(result)
{
	if (result.isError)
	{
		displayError(result.errorStr, 'error');
	}
	else
	{
		displayError(result.data, 'message');
	}
}

$(document).ready(function(){
	jQuery.fn.getSelectedItemID = function(){
		var selected = $(this).find(".ui-selected");
		var result = false;
		if (selected.length != 0)
			result = selected.attr('id');

		return result;
	};

	jQuery.fn.getSelectedItemName = function(){
		var selected = $(this).find(".ui-selected");
		var result = false;
		if (selected.length != 0)
			result = selected.text();

		return result;
	};

	jQuery.fn.getAllSelectedItems = function(){
		var result = "";
		$(this).find(".ui-selected").each(function(i, objItem){
			result += this.id + "|";
		});
		result = result.substring(0, result.length - 1);
		//alert(result);
		return result;
	};

	$("button").button();

	$("#jplayer-player").jPlayer({
		swfPath: "./client/js/lib"
	});
});
