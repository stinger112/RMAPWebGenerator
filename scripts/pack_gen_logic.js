function base_convert(value, base_from, base_to) { //http://vanchester.ru/converter.html
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

/*function opt_err_type() {
	
	var err_type = $("select[name='options:error:type']:selected").val();
	$.post("feeds.php", {opt: "error_type:"+err_type}, function (data) {
		
	});
}*/

function main() {
	
	
	/*---------------------Fields Objects----------------------*/
	//Fields of Packet
	objHeader = $("[name*='head']");
	objInstruction = $("input[name*='Instruction'][form]");
	objHeaderCRC = $("input[name*='crc:HeaderCRC']");
	
	objData = $("textarea[name*='data']");
	objDataCRC = $("input[name*='crc:DataCRC']");
	
	//Support Fields (options and buttoms)
	objErrorAllow = $("input[name*='error:allow']");
	objErrorType = $("select[name='options:error:type']");
	
	/*---------------------------------------------------------*/
	
	
	
	/*----------------------Events binding---------------------*/
	objErrorAllow.change(function () { //Оработчик блока добавления пакета	
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
	});
	
	objHeader.on('keyup blur change',function () { //Нерационально обрабатывать блок инструкции каждый раз, ведь значение блока меняется не для всех
		
		/*-----------------------Обработка блока инструкции-----------------------*/
		var instrStr = ""; 
		
		objInstruction.slice(0, 6).each(function () {
			
			if ($(this).filter(":checked").size())
				instrStr += "1";
			else
				instrStr += "0";
		});
		
		objInstruction.slice(6, 10).each(function () {
			if ($(this).filter(":checked").size())
				instrStr += $(this).val();
		});
		
		instrStr = base_convert(instrStr, 2, 16);
		$("input[name='03:head:Instruction']").val(instrStr);
		/*------------------------------------------------------------------------*/
		
		/*-----------------------Подсчет Header CRC-----------------------*/
		var headerStr = ""; 
		
		objHeader.not("[form]").each(function () {
			if ($(this).val())
				headerStr += $.trim($(this).val()) + " "; //Удаляем случайно попавшие пробелы
		});
		headerStr = $.trim(headerStr);
		
		$.post('feeds.php', {GiveMeCRC: headerStr}, function(data) {
			objHeaderCRC.val(data);
		});
		/*----------------------------------------------------------------*/
	});
	
	objData.on('keyup mousemove', function() { //Giving Data CRC
		var dataStr = $.trim(objData.val());
		
		$.post('feeds.php', {GiveMeCRC: dataStr}, function(data) {
			objDataCRC.val(data);
		});
	});

	/*---------------------------------------------------------*/
}

$(main); //Entering Point