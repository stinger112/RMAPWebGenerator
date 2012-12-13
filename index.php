<?php
	header('Content-Type: text/html; charset=utf-8');
	
	if (isset($_GET['gen']))
	{	
		include 'forms/pack_gen_form.inc';
	}
	elseif (isset($_GET['compare']))
	{
		include 'forms/pack_compare_form.inc';
	}
	elseif (isset($_GET['parse']))
	{
		include 'forms/pack_parse_form.inc';
	}
	else
	{
		include 'forms/main_menu.inc';
	}
?>