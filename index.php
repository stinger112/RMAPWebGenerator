<?php
	header('Content-Type: text/html; charset=utf-8');
	
	if (isset($_GET['gen'])):
	
		include 'forms/pack_gen_form.inc';
	
	elseif (isset($_GET['compare'])):

		include 'forms/pack_compare_form.inc';
	
	else:
?>
	
	Выберете тест:<br>
	<p><a href="?gen" style="text-decoration: none"><input type="button" value="Simple Packet Generator" /></a></p>
	<p><a href="?compare" style="text-decoration: none"><input type="button" value="Packets Compare" /></a></p>

<?php
	endif;	
?>