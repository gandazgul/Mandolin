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
		return result;
	};
	
	//all hover and click logic for buttons
	$(".ui-form button:not(.ui-state-disabled)")
	.hover(
		function(){
			$(this).addClass("ui-state-hover");
		},
		function(){
			$(this).removeClass("ui-state-hover");
		}
	)
	.mousedown(function(){
			$(this).parents('.fg-buttonset-single:first').find(".fg-button.ui-state-active").removeClass("ui-state-active");
			if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){$(this).removeClass("ui-state-active");}
			else {$(this).addClass("ui-state-active");}
	})
	.mouseup(function(){
		if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
			$(this).removeClass("ui-state-active");
		}
	});
});
