<?php
	require_once 'parser.php';
	error_reporting(0);
	
	if ($_POST['opt'] == "error") //Выдаем массив ошибок для выбора
	{
		foreach (RMAP::$errTable as $arValue)
		{
			if ((0 != $i) && (8 != $i))
				echo '<option value="'. $i .'">' . $arValue['error'] . "</option>\n";
			
			$i++;
		}
	}
	elseif (substr_count($_POST['opt'], "err_type"))
	{
		
	}
	
	
?>