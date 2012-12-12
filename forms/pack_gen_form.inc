<!DOCTYPE html>
<html>
<head>
<title>RMAP Simple Packet Generator</title>
<link type="text/css" rel="stylesheet" href="/css/gen.css">
</head>
<body>
<form action="handler.php" method="POST">

	<div id="address">
	<h2>Address:</h2>
	<input type="text" name="address" value="1" required> Target SpaceWire Addresses
	</div>
	
	<div id="header">
	<h2>Header:</h2>
	
	<select name="PID" size="1">
	<option value="0x01">Remote Memory Access Protocol</option>
	<!-- <option value="0d02">CCSDS Packet Transfer Protocol</option>
	<option value="0d238">GOES-R Reliable Data Delivery Protocol</option>
	<option value="0d239">Serial Transfer Universal Protocol</option> -->
	</select> Protocol Identifier field (8 bit)
	
	<p>Instruction field (8 bit include Reply Address Length field):</p>
		<input type="hidden"	name="instr_reserved"	value="0">
		<input type="hidden"	name="instr_command" 	value="1">
		<input type="checkbox"	name="instr_command"	value="1" checked disabled> Command<br>
		<input type="hidden"	name="instr_write"		value="0">
		<input type="checkbox"	name="instr_write"		value="1"> Write<br>
		<input type="hidden"	name="instr_verify"		value="0">
		<input type="checkbox"	name="instr_verify"		value="1"> Verify data<br>
		<input type="hidden"	name="instr_reply"		value="0">
		<input type="checkbox"	name="instr_reply"		value="1"> Reply<br>
		<input type="hidden"	name="instr_increment"	value="0">
		<input type="checkbox"	name="instr_increment"	value="1"> Increment<br>
	<p>Reply Address Length:
		<input name="instr_reply_addr_len" type="radio" value="00" checked> 0
		<input name="instr_reply_addr_len" type="radio" value="01"> 4
		<input name="instr_reply_addr_len" type="radio" value="10"> 8
		<input name="instr_reply_addr_len" type="radio" value="11"> 12
		Bytes
		
	<p><input type="text" name="key" 	value="0x00"> Key field (8 bit)
	<p><input type="text" name="reply_address"> Reply Address field (8 bit)	
	<p><input type="text" name="init_log_address"	value="0xfe"> Initiator Logical Address field (8 bit)
	<p><input type="text" name="trans_id"	disabled> Transaction Identifier field  (16 bit)
	<p><input type="text" name="trans_id"> Extended Address field (8 bit)
	<p><input type="text" name="trans_id"> Address field (32 bit)
	<p><input type="text" name="trans_id"> Data Length field (24 bit)
	<p><input type="text" name="trans_id"	disabled> Header CRC field (8 bit)
	</div>
	
	
	<div id="pack_body">
	<h2>Body:</h2>
	<p>
	<textarea name="body" cols="40" rows="10" placeholder="Данные в ASCI" readonly></textarea>
	</p>
	</div>
	
	<div id="options">
	<h2>Options:</h2>
	<input type="checkbox"	name="opt_view"		checked> Detail result view<br>
	<input type="checkbox"	name="opt_error"	checked> Allow error packet<br>	
	<p>Byte Fomats:
	<select name="opt_base" size="1">
	<option value="bin">Binary</option>
	<option value="oct">Octal</option>
	<option value="dec">Decymal</option>
	<option value="hex">Hexadecimal</option>
	<!-- <option value="ascii">ASCII</option> -->
	</select>
	</p>
	</div>
	
	<p>
	<input type="submit" value="Generate">
	<input type="reset" value="Defaults">
	</p>
	</form>
	
</body>
</html>