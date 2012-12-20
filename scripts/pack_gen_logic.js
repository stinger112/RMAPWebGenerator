var GiveErrorTypes = function() {	//Каждый раз выполняет GET запрос при нажатии галочки. Не придумал как сделать иначе =\
    if ($("input[name='options:error:allow']:checked").size())
    {
    	objErrorType.load('feeds.php?GiveMeErrorTypes');
    	objErrorType.removeAttr("disabled");
    }
    else
    {
    	objErrorType.empty();
    	objErrorType.attr("disabled", "disabled");
    }
};


function GiveCRC(packetString) { //Giving CRC based on packetString
	$.post('feeds.php', {GiveMeCRC: packetString}, function(data) {
		objDataCRC.val(data);
	});
};


/*function opt_err_type() {
	
	var err_type = $("select[name='options:error:type']:selected").val();
	$.post("feeds.php", {opt: "error_type:"+err_type}, function (data) {
		
	});
}*/

/*function fill_from_template(data) {
	
}*/


function main() {
	
	/*---------------------Fields Objects----------------------*/
	//Fields of Packet
	objData = $("textarea[name*='data']");
	objDataCRC = $("input[name*='data_crc']");
	
	//Support Fields (options and buttoms)
	objErrorAllow = $("input[name*='error:allow']");
	objErrorType = $("select[name='options:error:type']");
	/*---------------------------------------------------------*/
	
	
	/*----------------------Events binding---------------------*/
	objErrorAllow.change(GiveErrorTypes);
	
	objData.keyup(function() { GiveCRC(objData.val()); }).blur(function() {GiveCRC(objData.val());});
	//$("select[name='options:error:type']").change(opt_err_type);
	
	/*---------------------------------------------------------*/
}

$(main); //Entering Point