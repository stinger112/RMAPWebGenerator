<!DOCTYPE html>
<html>
<head>
<title>RMAP Simple Packet Generator</title>
<link type="text/css" rel="stylesheet" href="/css/gen.css">
	<script type="text/javascript" src="/scripts/jquery-1.8.3.js"></script>
	<script type="text/javascript" src="/scripts/jquery.form.js"></script>
	<script type="text/javascript" src="/scripts/javascript-sprintf.js"></script>
	<script type="text/javascript" src="/scripts/pack_gen_logic.js"></script>
</head>
<body>
<form id="form" action="handler.php?generate" method="POST">
	<div id="left">
		<div id="address">
			<h2>Address:</h2>
			<!-- -------------------Common Field------------------- -->
			<p><input type="text"	name="ADDRESS"> SpaceWire Address</p>	
			   <input type="hidden" name="HEADER"	>
			   <input type="hidden" name="DATA"	>
			<!-- -------------------------------------------------- -->
		</div>
		
		<div id="pack_body">
			<h2>Data:</h2>
			<p><textarea 			class="field data"	name="13:Data" rows="10" placeholder="Insert data (in hex)"></textarea></p>
			<p><input type="text"	class="field"		name="14:DataCRC"> Data CRC field (8 bit)
		</div>
		
		<div id="options">
			<h2>Options:</h2>
			<input type="checkbox"			name="view"		value="detail"	checked> Detail result view<br>
			<select	form=""	name="template_type"	size="1">
			<option value="0">Command executed successfully</option>
			<option value="2">Unused RMAP Packet Type or Command Code</option>
			<option value="4">Invalid Data CRC</option>
			<option value="5">Early EOP</option>
			<option value="6">Too much data</option>
			</select> Add template packet
			
			<p>Byte Fomats:
			<select name="base" size="1">
			<option value="2">Binary</option>
			<option value="8">Octal</option>
			<option value="10">Decymal</option>
			<option value="16" selected>Hexadecimal</option>
			</select>
			</p>
				<div id="buttons"><p>
					<input type="submit" value="Generate">
					<a href="index.php" style="text-decoration: none"><input type="button" value="Back"></a></p>
				</div>
		</div>
	</div>
	
	<div id="right">
		<div id="protocol"><h1>RMAP GENERATOR</h1></div>
		
		<div id="header">
			<p><input type="text"	class="field head"	name="01:TargetLogicalAddress"	value="fe" required> SpaceWire Logical Address<p>
			   <input type="hidden"	class="field head"	name="02:ProtocolID"	value="01">
			   <input type="hidden"	class="field head"	name="03:Instruction"	>
			<h2>Header:</h2>
			<p>Instruction field (8 bit include Reply Address Length field):</p>
				<input type="checkbox"	class="Instruction"	form="" name="reserved"		value="1"> Reserved<br>
				<input type="checkbox"	class="Instruction"	form="" name="command"		value="1" checked disabled> Command<br>
				<input type="checkbox"	class="Instruction"	form="" name="write"		value="1"> Write<br>
				<input type="checkbox"	class="Instruction"	form="" name="verify"		value="1"> Verify data<br>
				<input type="checkbox"	class="Instruction"	form="" name="reply"		value="1"> Reply<br>
				<input type="checkbox"	class="Instruction"	form="" name="increment"	value="1"> Increment<br>
			<p>Reply Address Length:
				<input type="radio"		class="Instruction"	form=""	name="reply_addr_len"	value="00"	checked> 0
				<input type="radio"		class="Instruction"	form=""	name="reply_addr_len"	value="01"> 4
				<input type="radio"		class="Instruction"	form=""	name="reply_addr_len"	value="10"> 8
				<input type="radio"		class="Instruction"	form=""	name="reply_addr_len"	value="11"> 12
				Bytes</p>
				
			<p><input type="text"	class="field head"	name="04:Key"				> Key field (1)
			<p><input type="text"	class="field head"	name="05:ReplyAddress"		> Reply Address field (1)	
			<p><input type="text"	class="field head"	name="06:LogicalAddress"	> Initiator Logical Address field (1)
			<p><input type="text"	class="field head"	name="07:TransactionID"		> Transaction Identifier field  (2)
			<p><input type="text"	class="field head"	name="09:ExtendedAddress"	> Extended Address field (1)
			<p><input type="text"	class="field head"	name="10:Address"			> Address field (4)
			<p><input type="text"	class="field head"	name="11:DataLength"		> Data Length field (3)
			<p><input type="text"	class="field"		name="12:HeaderCRC"			> Header CRC field (1)
		</div>
	</div>
	
	<div id="result"></div>

</form>
</body>
</html>