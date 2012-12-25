<?php
	header('Content-Type: text/html; charset=utf-8');
	
	if (isset($_GET['generate']))
	{
		switch ($_GET['generate'])
		{
			case 'RMAP':
				include 'forms/pack_gen_form.inc';
				break;
			default:
				include 'forms/pack_gen_common_form.inc';
				break;
		}
	}
	elseif (isset($_GET['compare']))
	{	
		$original_pack = $_POST['packet'];
		include 'forms/pack_compare_form.inc';
	}

	elseif (isset($_GET['parse']))
		include 'forms/pack_parse_form.inc';

	else
		include 'forms/main_menu.inc';
?>