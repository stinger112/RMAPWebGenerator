<!DOCTYPE html>
<html>
<head>
<title>RMAP Simple Packet Generator</title>
<link type="text/css" rel="stylesheet" href="/css/gen.css">
	<script type="text/javascript" src="/scripts/jquery-1.8.3.js"></script>
	<script type="text/javascript" src="/scripts/pack_gen_common_logic.js"></script>
</head>
<body>
<form id="form" action="index.php?compare" method="POST">
	
	<input type="hidden" name="packet" value="">
	
	<div id="left">
		<div id="address">
			<h2>Address:</h2>
			<p><input type="text" class="packet" name="00:SpaceWireTargetAddress"> SpaceWire Address</p>
			<p><input type="text" class="packet" name="01:TargetLogicalAddress" value="fe" required> SpaceWire Logical Address<p>
		</div>
		
		<div id="options">
			<h2>Options:</h2>
			<p><input type="number" name="length" min="0" value="10">Body Length</p>
			<div id="buttons">
				<p>
				<input type="submit" value="Generate">
				<input type="button" VALUE="Back" onClick="history.go(-1);">
				</p>
			</div>
		</div>
	</div>
	
	<div id="right">
		<div id="protocol">
			<h1>SpaceWire Generator</h1>
		</div>
		
		<div id="pack_body">
			<h2>Packet Body:</h2>
			<p><textarea	class="packet"	name="02:packet:Body" rows="15" placeholder="Packet Data"></textarea></p>
			<!-- <p>
			<input name="end_char" type="radio" value="EOP" checked disabled> EOP
			<input name="end_char" type="radio" value="EEP" disabled> EEP
			</p> -->
		</div>
	</div>

</form>
</body>
</html>