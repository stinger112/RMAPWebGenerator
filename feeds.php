<?php
	require_once 'parser.php';
	error_reporting(0);
	
	if (isset($_GET['GiveMeErrorTypes']))
	{
		foreach (RMAP::$errTable as $arValue)
		{
			if ((0 != $i) && (8 != $i))
				echo '<option value="'. $i .'">' . $arValue['error'] . "</option>\n";
			
			$i++;
		}
	}
	
	elseif (isset($_GET['GiveMeErrorPacket']))
	{
		switch ($_GET['GiveMeErrorPacket'])
		{
			case 1:
				break;
			default:
				break;
		}
	}
	
	elseif (isset($_POST['GiveMeCRC'])) //Give throw POST, because Data perhaps very massive for GET question
	{
		$arData = explode(' ', $_POST['GiveMeCRC']);
		$crc = RMAP::calculateCRC($arData);
		echo $crc;
	}
?>