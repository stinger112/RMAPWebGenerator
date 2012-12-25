function base_convert(value, base_from, base_to) { //http://vanchester.ru/converter.html
	base_from = parseInt(base_from);
	base_to = parseInt(base_to);
	num = parseInt(value, base_from);
	amount = num.toString(base_to);
	return amount;
}

function main() {
	
/*	var collections = { //Массив наборов элементов, соответствующих определенному протоколу
			RMAP: $("#header, #pack_body, #RMAP"),
			common: $("#common")
		};*/
	/*---------------------Fields Objects----------------------*/
	objProtocol = $("[name='protocol']");
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
	
/*	var prevCollection; //Набор набор уникальных элементов страницы, характерных для определенного протокола
	var currentCollection = collections[objProtocol.val()]; //Текущий набор уникальных элементов страницы, характерных для определенного протокола
*/	
	/*------------Функции захвата данных с сервера-------------*/
	function GiveMeCRC(string, targetObj) {
		$.post('feeds.php', {GiveMeCRC: string}, function(data) {
			targetObj.val(data);
		});
	}
	
	function GiveMeResult() { //Получаем результат и добавляем его в нижнее в footer
			$("#form").ajaxSubmit({
				target: "#result",
				url: "feeds.php?GiveMeResult"
			});	
	}
	
	/*----------------------Events binding---------------------*/
/*	objProtocol.change(function () {
		if (collections[objProtocol.val()])
		{
			prevCollection = currentCollection;
			currentCollection.detach();
			currentCollection = collections[objProtocol.val()];
			alert(objProtocol.val());
			currentCollection.appendTo('body');
		}
	});*/
	
	objErrorAllow.toggle(function () { //Оработчик блока добавления пакета
	    objErrorType.load('feeds.php?GiveMeErrorTypes');
	    objErrorType.removeAttr("disabled");
	}, function () {
		objErrorType.empty();
    	objErrorType.attr("disabled", "disabled");
	});
	
	$("#form").on('mousemove', function () { //Обрабатываем изменение формы
		GiveMeResult();
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
		
		/*-----------------------Подсчет Header CRC-----------------------*/
		var headerStr = ""; 
		
		objHeader.not("[form]").each(function () {
			if ($(this).val())
				headerStr += $.trim($(this).val()) + " "; //Удаляем случайно попавшие пробелы
		});
		headerStr = $.trim(headerStr);
		GiveMeCRC(headerStr, objHeaderCRC);
	});
	
	objData.on('keyup mousemove', function() { //Giving Data CRC
		var dataStr = $.trim(objData.val());		
		GiveMeCRC(dataStr, objDataCRC);
	});

	/*---------------------------------------------------------*/
}

$(main); //Entering Point