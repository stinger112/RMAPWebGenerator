function base_convert(value, base_to, base_from) { //http://vanchester.ru/converter.html
	//Преобразуем данные в Integer
	base_from = parseInt(base_from);
	base_to = parseInt(base_to);
	//преобразуем данные по основанию base_from в десятичную систему
	num = parseInt(value, base_from);
	//преобразуем данные из десятичной в систему по указанному основанию base_to
	amount = num.toString(base_to);
	//выводим результат
	return amount;
}

function GiveErrorTypes() {
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
}


function GiveCRC(packetString) { //Giving CRC based on packetString
	$.post('feeds.php', {GiveMeCRC: packetString}, function(data) {
		objDataCRC.val(data);
	});
}

/*function opt_err_type() {
	
	var err_type = $("select[name='options:error:type']:selected").val();
	$.post("feeds.php", {opt: "error_type:"+err_type}, function (data) {
		
	});
}*/

function main() {
	
	/*---------------------Fields Objects----------------------*/
	//Fields of Packet
	objData = $("textarea[name*='data']");
	objDataCRC = $("input[name*='data_crc']");
	objInstruction = $("input[name*='Instruction']");
	
	//Support Fields (options and buttoms)
	objErrorAllow = $("input[name*='error:allow']");
	objErrorType = $("select[name='options:error:type']");
	
	/*---------------------------------------------------------*/
	
	
	/*----------------------Events binding---------------------*/
	objErrorAllow.change(GiveErrorTypes);
	
	objData.keyup(function() { GiveCRC(objData.val()); }).blur(function() {GiveCRC(objData.val());});
	
	/*objInstruction.change(function () {
		
		var instrStr = objInstruction.length;
		for (var i = 0; i < objInstruction.length ; i++)
		{
			var instrStr = objInstruction.index(i);
		}
		
		alert(instrStr);
	});
	*/
	
	//$("select[name='options:error:type']").change(opt_err_type);
	
	/*---------------------------------------------------------*/
}

$(main); //Entering Point