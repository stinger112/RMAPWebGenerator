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
	<div id=left>
		<div id="address">
		<h2>Address:</h2>
		<p><input type="text" name="00:address:SpaceWireTargetAddress"> SpaceWire Address</p>
		<p><input type="text" name="01:head:TargetLogicalAddress" value="fe" required> SpaceWire Logical Address<p>
		</div>
		
		<div id="pack_body">
		<h2>Data:</h2>
		<p><textarea 			name="13:data" rows="10" placeholder="Insert data (in hex)">01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17</textarea></p>
		<p><input type="text"	name="14:crc:DataCRC" value="56"> Data CRC field (8 bit)
		<!-- <p>
		<input name="end_char" type="radio" value="EOP" checked disabled> EOP
		<input name="end_char" type="radio" value="EEP" disabled> EEP -->
		</p></div>
		
		<div id="options">
		<h2>Options:</h2>
		<!-- <input type="checkbox"	form="" name="options:view"	checked> Detail result view<br> -->
		<input type="checkbox"	form="" name="options:error:allow">
		<select name="options:error:type" form="internal"	size="1" disabled></select> Add error packet<br>
		
		<p>Byte Fomats:
		<select name="base" size="1">
		<option value="2">Binary</option>
		<option value="8">Octal</option>
		<option value="10">Decymal</option>
		<option value="16" selected>Hexadecimal</option>
		<!-- <option value="ascii">ASCII</option> -->
		</select>
		</p>
			<div id="buttons"><p>
			<input type="submit" value="Generate">
			<!-- <input type="reset" value="Defaults"> -->
			<a href="index.php" style="text-decoration: none"><input type="button" value="Back"></a></p>
			</div>
		</div>
	</div>
	
	
	<div id="header">
		<h2>Header:</h2>
		<select name="02:head:ProtocolID" size="1">
		<option value="01">Remote Memory Access Protocol</option>
		<!-- <option value="02">SpaceWire Common</option> -->
		<!-- <option value="238">GOES-R Reliable Data Delivery Protocol</option>
		<option value="239">Serial Transfer Universal Protocol</option> -->
		</select>
		
		Protocol Identifier field (8 bit)

		<p>Instruction field (8 bit include Reply Address Length field):</p>
			<input type="hidden"	name="03:head:Instruction" 	value="6c">
			<input type="checkbox"	form="" name="head:Instruction:reserved"	value="1"> Reserved<br>
			<input type="checkbox"	form="" name="head:Instruction:command"		value="1" checked> Command<br>
			<input type="checkbox"	form="" name="head:Instruction:write"		value="1" checked> Write<br>
			<input type="checkbox"	form="" name="head:Instruction:verify"		value="1"> Verify data<br>
			<input type="checkbox"	form="" name="head:Instruction:reply"		value="1" checked> Reply<br>
			<input type="checkbox"	form="" name="head:Instruction:increment"	value="1" checked> Increment<br>
		<p>Reply Address Length:
			<input type="radio"		form=""	name="head:Instruction:reply_addr_len"	value="00" checked> 0
			<input type="radio"		form=""	name="head:Instruction:reply_addr_len"	value="01"> 4
			<input type="radio"		form=""	name="head:Instruction:reply_addr_len" 	value="10"> 8
			<input type="radio"		form=""	name="head:Instruction:reply_addr_len"	value="11"> 12
			Bytes</p>
			
		<p><input type="text" name="04:head:Key"				value="00"				> Key field (1)
		<p><input type="text" name="05:head:ReplyAddress"								> Reply Address field (1)	
		<p><input type="text" name="06:head:LogicalAddress"		value="67"				> Initiator Logical Address field (1)
		<p><input type="text" name="07:head:TransactionID"		value="00 00"			> Transaction Identifier field  (2)
		<p><input type="text" name="09:head:ExtendedAddress"	value="00"				> Extended Address field (1)
		<p><input type="text" name="10:head:Address"			value="a0 00 00 00"		> Address field (4)
		<p><input type="text" name="11:head:DataLength"			value="00 00 10"		> Data Length field (3)
		<p><input type="text" name="12:crc:HeaderCRC"			value="9f"				> Header CRC field (1)
	</div>
	
</form>
</body>
</html>