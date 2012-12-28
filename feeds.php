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
				//Unused RMAP Packet Type or Command Code
				2 => array(	'fe 01 40',
							'fe 01 44',
							'fe 01 50',
							'fe 01 54',
							'fe 01 58',
							'fe 01 c8'
						),
				//Invalid Data CRC
				4 => array(	'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb',
							'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 1e 75 55 c2 53 d5 1e 40 b1 10 72 15 5 48 65 44 38 df 61 89 47 1c f2 2f 26 67 9f ad dd 62 b7 a4 5a',
							'fe 01 6d 00 a6 71 ce 8e 67 00 00 00 a0 00 18 00 00 00 36 a8 5f 2d a6 12 13 b9 40 e6 cb 65 42 ef 77 c7 d1 31 f5 fb 3c 95 36 be 9c 30 5b b7 4f fd e 71 9d f7 81 99 3 ab 18 a1 81 72 20 b1 d5 74 e6 3b b2 76 32 2e ea 46 5b d3 34'), 
				//Early EOP
				5 => array(	'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f',
							'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab',
							'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 1e 75 55 c2 53 d5 1e 40 b1 10 72 15 5 48 65',
							'fe 01 48 00 67 00 00 00 a0 00 00 00 00',
							'fe 01 64 00 67 ad 13 00 a0 00 00 ff 00 00 0f 35 be cf 66 32 4d ef 4a ad 76 0',
							'fe 01 4a 00 10 38 ab 21 25 de 65 97 67 00 df'
						),
				//Too much data
				6 => array(	'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56 b2 53 1c c6 d5 96 7c cb 85 44',
							'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 1e 75 55 c2 53 d5 1e 40 b1 10 72 15 5 48 65 44 38 df 61 89 47 1c f2 2f 26 67 9f ad dd 62 b7 a4 68 cf 3f 19 17 f8 10 fd 4e 7f df',
							'fe 01 48 00 67 00 00 00 a0 00 00 00 00 00 0e 6c 2d 40 f 2a c7 4 d7 bf 17 1c 3c 59 ef a6',
							'fe 01 64 00 67 ad 13 00 a0 00 00 ff 00 00 0f 35 be cf 66 32 4d ef 4a ad 76 05 34 12 43 34 43 35 bc 3d fe af 2e f3',
							'fe 01 4a 00 10 38 ab 21 25 de 65 97 67 00 df 00 a0 00 10 00 00 00 10 36 71 94 42 34'
						)
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