function base_convert(value, base_from, base_to) { //http://vanchester.ru/converter.html
	base_from = parseInt(base_from);
	base_to = parseInt(base_to);
	num = parseInt(value, base_from);
	amount = num.toString(base_to);
	return amount;
}

/*------------------------Define functions-------------------------*/
function GiveMeCRC(string) {
	var crc = "";

	crc = $.ajax({
		  url: 'feeds.php',
		  type: 'POST',
		  data: {GiveMeCRC: string},
		  async: false
	}).responseText;
	
	return crc;
}

function CalculateHeader () //Calculate Header
{
	var headerStr = ""; 
	
	$(".head").each(function () {
		if ($(this).val())
			headerStr += $.trim($(this).val()) + " "; //Удаляем случайно попавшие пробелы
	});
	headerStr = $.trim(headerStr);
	
	return headerStr;
}

function CalculateCommonFields ()
{
	var addressStr = $.trim($("input[name='ADDRESS']").val());
	$("input[name='ADDRESS']").val(addressStr); 
	
	//Save Header
	var headerStr = CalculateHeader()+ " " + $.trim($("input[name*='HeaderCRC']").val());
	$("[name='HEADER']:hidden").val($.trim(headerStr)); 
	
	//Save Data
	var dataStr = $.trim($(".field[name='13:Data']").val()) + " " + $.trim($("input[name*='DataCRC']").val());
	$("[name='DATA']").val($.trim(dataStr)); 
}
/*-----------------------------------------------------------------*/

function main() {
	/*Событие добавления шаблона (изменение поля шаблона). Включает получение данных с сервера в формате JSON
	и заполнение этими данными соответствующих полей формы.*/
	$("select[name='template_type']").change(function () { 
		
		var templateCode = $(this).val();
		
		$.getJSON('feeds.php', {GiveMeJSONMap: templateCode}, function (data) { //Getting JSON Packet Map from server and parse it
			var JSONPacketMap = data;

			if (JSONPacketMap)
			{
				$(".field").each(function () { //Вставляем в каждое поле значение согласно карте		
					var name = $(this).attr("name").match(/[a-zA-Z]*$/);

					var str = JSONPacketMap[name];
					$(this).attr("value", ""); //Delete old data
					$(this).val(str);
					
					if ('Instruction' == name) //Раскидываем значения на блок инструкции
					{
						var instr = sprintf("%08d", base_convert(str, 16, 2));
						
						$(".Instruction").slice(0, 6).each(function (index) {	
							if ('1' == instr[index])
								$(this).attr("checked", "checked");
							else
								$(this).removeAttr("checked");
						});
						
						$(".Instruction").filter("[value='"+instr[6]+instr[7]+"']").attr("checked", "checked");
					}
				});	
			}
		});
	});
	
	
	/*Обрабатываем изменение формы. Получаем результат и добавляем его в footer*/
	$("#result").on('mouseenter', function () {
		CalculateCommonFields();
		
		$("#form").ajaxSubmit({
			target: "#result",
			url: "feeds.php?GiveMeResult"
		});	
	});
	
	
	/*Обработчики изменения заголовка. После каждого изменения подсчитывает CRC код заголовка
	и вносит его в соответствующее поле*/
	$(".Instruction").click(function () { //Обработчик блока инструкции
		var instrStr = ""; 
		
		$(".Instruction").slice(0, 6).each(function () {		
			if (this.checked)
				instrStr += "1";
			else
				instrStr += "0";
		});
		
		instrStr += $(".Instruction").slice(6, 10).filter(":checked").val();
		
		instrStr = base_convert(instrStr, 2, 16);
		$("input[name*='Instruction']").val(instrStr);
		
		var crc = GiveMeCRC(CalculateHeader()); //Calculate and save Header CRC
		$("input[name*='HeaderCRC']").val(crc);
	});
	
	$(".head").on('keyup blur change', function () {
		var crc = GiveMeCRC(CalculateHeader()); //Calculate and save Header CRC
		$("input[name*='HeaderCRC']").val(crc);
	});
	
	
	/*Обработчик изменения блока данных, заполняющий DataCRC и DataLength*/
	$("[name='Data']").on('keyup blur', function() { 	
		var dataStr = $.trim($(".data").val());
		
		$("input[name*='DataCRC']").val(GiveMeCRC(dataStr)); //Giving Data CRC
		
		//Calclulate DataLength
		var dataLen = dataStr.split(' ').length; 		
		var tmp = sprintf("%06x", dataLen);
		$("input[name*='DataLength']").val(tmp[0]+tmp[1]+" "+tmp[2]+tmp[3]+" "+tmp[4]+tmp[5]);
	});
}

$(main); //Entering Point