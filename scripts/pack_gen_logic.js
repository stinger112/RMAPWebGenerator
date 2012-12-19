function opt_error() {

    if ($("input[name='options:error:allow']:checked").size())
    {
    	$("select[name='options:error:type']").load('feeds.php', {opt: "error"});
    	$("select[name='options:error:type']").removeAttr("disabled");
    }
    else
    {
    	$("select[name='options:error:type']").empty();
    	$("select[name='options:error:type']").attr("disabled", "disabled");
    }
}
function opt_err_type() {
	
	var err_type = $("select[name='options:error:type']:selected").val();
	$.post("feeds.php", {opt: "error_type:"+err_type}, function (data) {
		
	});
}

function fill_from_template(data) {
	
}

function main() {

	/*---------------------Events handling---------------------*/
	
	$("input[name='options:error:allow']").change(opt_error);
	$("select[name='options:error:type']").change(opt_err_type);
	$("select[name='options:error:type']").change(opt_err_type);
	
	/*---------------------------------------------------------*/
}

$(main); //Entering Point