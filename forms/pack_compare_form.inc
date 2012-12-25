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
	<form action="handler.php?compare" method="POST">
	<textarea name="original_pack" cols="40" rows="30" placeholder="Insert original packet"><?php if ($original_pack) echo $original_pack?></textarea>
	<textarea name="received_pack" cols="40" rows="30" placeholder="Insert received packet"></textarea>
	<p><input type="submit" value="Сравнить"><input type="button" VALUE="Back" onClick="history.go(-1);"></p>
	</form>
	</div>
</body>
</html>