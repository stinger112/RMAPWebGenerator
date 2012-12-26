<?php
	require_once 'parser.php';
	error_reporting(0);
	
	if (isset($_GET['GiveMeResult']))
	{
		ksort($_POST);
		foreach ($_POST as $key => $value)
		{
			if ($value && preg_match('/^(\d+):.*/', $key))
			{
				if (substr_count($key, 'SpaceWireTargetAddress'))
					$addrLen = count(explode(' ', trim($value)));
				
				$arDrawPacket[] = $value;
			}	
		}
		$packetStr = trim(implode(' ', $arDrawPacket));
		$packetObj = Packet::Factory($packetStr, $addrLen);
		
		echo "<h3>Packet:</h3>" . "<font color='#FFC0CB'></font> " . $packetObj->getPacketString($_POST['base']);
				
		if ($_POST['view'])
		{
			$packetObj->parse();
			$packetObj->showResult();
		}
	}
	
	elseif (isset($_GET['GiveMeJSONMap']))
	{
		$arPacketsSample = array (
				//Пакеты команды
				0 => 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56', //Command executed successfully
				4 => 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb', //Invalid Data CRC
				5 => 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f', //Early EOP
				6 => 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56 ff ff ff ff ff ff ff', //Too much data
		);
				
		$packetStr = $arPacketsSample[$_GET['GiveMeJSONMap']];
		
		if ($packetStr)
		{
			$packetObj = Packet::Factory($packetStr)->parse();
			$Map = $packetObj->getMap('decoded');
			
			if ($Map['ExcessBytes']) //Делаем редактирование карты для случая Too much data (Пристыковываем ExcessBytes к последнему полю карты)
			{
				end($Map);
				prev($Map);		
				$key = key($Map); //Пришлось сделать так, через current($Map) нет доступа к значению элемента 
				$Map[$key] .= " ". $Map['ExcessBytes'];
				unset($Map['ExcessBytes']);
			}
			
			echo json_encode($Map);
		}
	}
	##################################################Exchange throw POST for massive question##################################################
	elseif ($_POST['GiveMeCRC'])
	{
		$arData = explode(' ', $_POST['GiveMeCRC']);
		$crc = RMAP::calculateCRC($arData);
		echo $crc;
	}
?>