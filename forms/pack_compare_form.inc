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
	<textarea name="original_pack" cols="40" rows="30" placeholder="Insert original packet"></textarea>
	<textarea name="received_pack" cols="40" rows="30" placeholder="Insert received packet"></textarea>
	<p><input type="submit" value="Сравнить"><a href="?gen" style="text-decoration: none"><input type="button" value="Back" /></a></p>
	</form>
	</div>
</body>
</html>