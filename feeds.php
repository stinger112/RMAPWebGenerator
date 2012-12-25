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
	
	elseif (isset($_GET['GiveMeResult']))
	{
		//var_dump($_POST);
		ksort($_POST);
	
		foreach ($_POST as $key => $value)
		{
			if ($value && preg_match('/^(\d+):.*/', $key))
				if (substr_count($key, 'SpaceWireTargetAddress'))
				$address = trim($value);
			else
				$arDrawPacket[] = $value;
		}
		$packetStr = trim(implode(' ', $arDrawPacket));
	
		$packetObj = Packet::Factory($packetStr)->parse();
	
		echo "<h3>Packet:</h3>" . "<font color='#FFC0CB'>$address</font> " . $packetObj->getPacketString($_POST['base']);
	
		$packetObj->showResult();
	}
	
	elseif ($_GET['GiveMePacketString'])
	{
		$packetStr = trim($_POST['packet']);
		echo Packet::Factory($packetStr)->getPacketString($_POST['base']);
	}
	##################################################Exchange throw POST for massive question##################################################
	elseif ($_POST['GiveMeCRC'])
	{
		$arData = explode(' ', $_POST['GiveMeCRC']);
		$crc = RMAP::calculateCRC($arData);
		echo $crc;
	}
?>