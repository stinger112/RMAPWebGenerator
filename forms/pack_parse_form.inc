<!DOCTYPE html>
<html>
<head>
<title>Packet Compare</title>
<style type="text/css">
	textarea {
	resize: none;
	}
</style>
</head>
<body>
	<div id="form">
	<form action="handler.php?parse" method="POST">
	<textarea name="packet" cols="40" rows="30" placeholder="Insert packet"><?php if ($included_pack) echo $included_pack ?></textarea>
	<p><input type="submit" value="Parse"></p>
	</form>
	<input type="button" VALUE="Back" onClick="history.go(-1);"> 
	</div>
</body>
</html>