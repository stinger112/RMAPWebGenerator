function base_convert(value, base_from, base_to) { // http://vanchester.ru/converter.html
	base_from = parseInt(base_from);
	base_to = parseInt(base_to);
	num = parseInt(value, base_from);
	amount = num.toString(base_to);
	return amount;
}

function randomNumber(m,n) { // http://www.cyberguru.ru/web/html/javascript-samples-page15.html
  m = parseInt(m);
  n = parseInt(n);
  return Math.floor( Math.random() * (n - m + 1) ) + m;
}

function ComposePacketHandler() {
	/*-----Create and save packet string--------*/
	var packetStr = "";
	
	$("[class='packet']").each(function () {
		packetStr += $(this).val() + " ";
	});
	packetStr = $.trim(packetStr);
	
	$("input[name='packet']").val(packetStr);
}

function NumGenHandler() {
	/*-------------Update Body field-------------*/
	var string = "";
	
	for (var i=0 ; i < $("input[name='length']").val() ; i++)
	{
		string += base_convert(randomNumber(0, 255), 10, 16) + " ";
	}
	string = $.trim(string);
	
	$("textarea[name*='Body']").text(string);
	
	ComposePacketHandler();
}

$(function () {
	NumGenHandler();
	ComposePacketHandler();
	
	$("input[name='length']").on('change blur', NumGenHandler);
	$("[class='packet']").on('keyup blur', ComposePacketHandler);
});