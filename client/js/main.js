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

function displayError(data)
{
	$("#errorDiv").text(data).show().animate({height: "15px", padding: "10px"}, 500, "linear", hideError);
}
