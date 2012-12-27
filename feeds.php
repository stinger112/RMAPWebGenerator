<?php
	require_once 'parser.php';
	error_reporting(0);
	
	if (isset($_GET['GiveMeResult']))
	{
	//	ksort($_POST);
	//	foreach ($_POST as $key => $value)
	//	{
	//		if ($value && preg_match('/^(\d+):.*/', $key))
	//		{
	//			if (substr_count($key, 'SpaceWireTargetAddress'))
	//				$addrLen = count(explode(' ', trim($value)));
	//			
	//			$arDrawPacket[] = $value;
	//		}	
	//	} 
	//	$packetStr = trim(implode(' ', $arDrawPacket));
	
		echo "<h3>Packet:</h3>";
		
		if ($_POST['ADDRESS'])
		{
			$addrLen = count(explode(' ', $_POST['ADDRESS']));
			$packetStr .= $_POST['ADDRESS'] . " ";
			echo "<font color='#FFC0CB'>" . Packet::PacketFormatter($_POST['ADDRESS'], $_POST['base']) . "</font> ";
		}
		
		if ($_POST['HEADER'])
		{
			$packetStr .= $_POST['HEADER'] . " ";
			echo "<font color='#90EE90'>" . Packet::PacketFormatter($_POST['HEADER'], $_POST['base']) . "</font> ";
		}
		

		if ($_POST['DATA'])
		{
			$packetStr .= $_POST['DATA'];
			echo "<font color='#87CEEB'>" . Packet::PacketFormatter($_POST['DATA'], $_POST['base']) . "</font> ";
		}
		
		$packetObj = Packet::Factory($packetStr, $addrLen);
				
		if ($_POST['view'])
		{
			$packetObj->parse();
			$packetObj->showResult();
		}
	}
	
	elseif (isset($_GET['GiveMeJSONMap']))
	{
		$arPacketsSample = array (
				//Command executed successfully
				0 => array(	'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56',
							'fe 01 4c 00 67 00 01 00 a0 00 00 00 00 00 10 c9',
							'fe 01 6e 00 00 99 aa bb cc dd ee 00 67 00 02 00 a0 00 00 10 00 00 10 7f a0 a1 a2 a3 a4 a5 a6 a7 a8 a9 aa ab ac ad ae af b4',
							'fe 01 4d 00 99 aa bb cc 67 00 03 00 a0 00 00 10 00 00 10 f7'
						),
				2 => arry(	),			
				4 => array(	'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb'), //Invalid Data CRC
				5 => array(	'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f'), //Early EOP
				6 => array(	'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56 ff ff ff ff ff ff ff'), //Too much data
		);
		
		$len = count($arPacketsSample[$_GET['GiveMeJSONMap']]);
		$packetStr = $arPacketsSample[$_GET['GiveMeJSONMap']][rand(0, $len - 1)];
		
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