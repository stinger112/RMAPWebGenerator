<!DOCTYPE html>
<html>
<head>
<title>RMAP Simple Packet Generator</title>
<link type="text/css" rel="stylesheet" href="/css/gen.css">
	<script type="text/javascript" src="/scripts/jquery-1.8.3.js"></script>
	<script type="text/javascript" src="/scripts/pack_gen_logic.js"></script>
</head>
<body>
<form action="handler.php?generate" method="POST">

	<div id="hidden" hidden="hidden">
		<!------------------------Instructions------------------------>
		<input type="hidden"	name="03:head:instr:reserved"	value="0">
		<input type="hidden"	name="03:head:instr:command" 	value="1">
		<input type="hidden"	name="03:head:instr:write"		value="0">
		<input type="hidden"	name="03:head:instr:verify"		value="0">
		<input type="hidden"	name="03:head:instr:reply"		value="0">
		<input type="hidden"	name="03:head:instr:increment"	value="0">
		
		<input name="end_char" type="hidden" value="EOP">
	</div>

	<div id=left>
		<div id="address">
		<h2>Address:</h2>
		<p><input type="text" name="01:address:spw_trgt_addr" disabled> SpaceWire Address</p>
		<p><input type="text" name="02:address:logic_addr" value="fe" required> SpaceWire Logical Address<p>
		</div>
		
		<div id="pack_body">
		<h2>Data:</h2>
		<p><textarea 	id="data"	name="13:data" rows="10" placeholder="Insert data (in hex)"></textarea></p>
		<p><input type="text"	name="14:data_crc"> Data CRC field (8 bit)
		<!-- <p>
		<input name="end_char" type="radio" value="EOP" checked disabled> EOP
		<input name="end_char" type="radio" value="EEP" disabled> EEP -->
		</p></div>
		
		<div id="options">
		<h2>Options:</h2>
		<input type="checkbox"	name="options:view"	checked> Detail result view<br>
		<input type="checkbox"	name="options:error:allow">
		<select name="options:error:type" size="1" disabled ></select> Allow error packet<br>
		
		<p>Byte Fomats:
		<select name="options:base" size="1">
		<option value="bin">Binary</option>
		<option value="oct">Octal</option>
		<option value="dec">Decymal</option>
		<option value="hex" selected>Hexadecimal</option>
		<option value="ascii">ASCII</option>
		</select>
		</p>
			<div id="buttons"><p>
			<input type="submit" value="Generate">
			<input type="reset" value="Defaults">
			<a href="index.php" style="text-decoration: none"><input type="button" value="Back"></a></p>
			</div>
		</div>
	</div>
	
	<div id="header">
		<h2>Header:</h2>
		
		<select name="01:head:PID" size="1">
		<option value="0x01">Remote Memory Access Protocol</option>
		<!-- <option value="0d02">CCSDS Packet Transfer Protocol</option>
		<option value="0d238">GOES-R Reliable Data Delivery Protocol</option>
		<option value="0d239">Serial Transfer Universal Protocol</option> -->
		</select>
		
		Protocol Identifier field (8 bit)

		<p>Instruction field (8 bit include Reply Address Length field):</p>
			<input type="checkbox"	name="03:head:instr:reserved"	value="1" disabled> Reserved<br>
			<input type="checkbox"	name="03:head:instr:command"	value="1" checked disabled> Command<br>
			<input type="checkbox"	name="03:head:instr:write"		value="1"> Write<br>
			<input type="checkbox"	name="03:head:instr:verify"		value="1"> Verify data<br>
			<input type="checkbox"	name="03:head:instr:reply"		value="1"> Reply<br>
			<input type="checkbox"	name="03:head:instr:increment"	value="1"> Increment<br>
		<p>Reply Address Length:
			<input name="03:head:instr:reply_addr_len" type="radio" value="00" checked> 0
			<input name="03:head:instr:reply_addr_len" type="radio" value="01"> 4
			<input name="03:head:instr:reply_addr_len" type="radio" value="10"> 8
			<input name="03:head:instr:reply_addr_len" type="radio" value="11"> 12
			Bytes</p>
			
		<p><input type="text" name="04:head:key"			value="0x00"> Key field (8 bit)
		<p><input type="text" name="05:head:reply_addr"					> Reply Address field (8 bit)	
		<p><input type="text" name="06:head:init_log_addr"	value="0xfe"> Initiator Logical Address field (8 bit)
		<p><input type="text" name="07:head:trans_id"		value="0x00 0x00"	disabled> Transaction Identifier field  (16 bit)
		<p><input type="text" name="09:head:ext_addr"		value="0xfe"			> Extended Address field (8 bit)
		<p><input type="text" name="10:head:address"		value="0xfe"			> Address field (32 bit)
		<p><input type="text" name="11:head:data_len"		value="0x00"			> Data Length field (24 bit)
		<p><input type="text" name="12:head:header_crc"						disabled> Header CRC field (8 bit)
	</div>
	
</form>
</body>
</html>