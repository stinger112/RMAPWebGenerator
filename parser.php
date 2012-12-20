<?php 

	class Packet //!!Все внутренние массивы хранят hex-значения!!
	{	
		private $arResult; //Хранит массив строк-сведений о пакете
		private $arPacket; //Хранит побайтовый массив пакета
		
		protected function __construct($packStr)
		{
			$arPack = explode(" ", $packStr);
			$this->PacketArray($arPack);
			
			$this->setResult("Packet length:\t" . count($this->PacketArray()));
		}
		
		static public function Factory($packStr, $addrLenInBytes = 0) //Метод-фабрика (Производит дифференциацию пакетов по типам)
		{
			if ($packStr && is_string($packStr))
			{	
				$arPacket = explode(' ', $packStr);
				switch ($arPacket[1])
				{
					case RMAP::$protocolID:
						$packet = new RMAP($packStr);
						break;
					default:
						$packet = new Packet($packStr);
						break;
				}
				
				return $packet;
			}
			else 
				throw new RuntimeException("Packet didn't created (wrong string received)");
		}
		
		##################################################Методы доступа##################################################
		protected function setResult($result, $arrayName = NULL)
		{
			$arrayName ? $this->arResult[$arrayName][] = $result : $this->arResult[] = $result;
			
			return $this->arResult;
		}
		
		public function getResult()
		{
			return $this->arResult;
		}
		
		public function showResult() //Отображает результаты в браузер
		{
			foreach($this->getResult() as $key => $valueExt)
			{
				if (is_array($valueExt))
				{
					echo "<font color='red'>$key: </font>";
					foreach ($valueExt as $valueInt)
					{
					echo "$valueInt ";
					}
					echo "<br>";
				}
				else
					echo "$valueExt<br>";
			}
		}
				
		protected function PacketArray($arPack = NULL) //Универсальный метод доступа к массиву пакета
		{
			if ($arPack)
				$this->arPacket = $arPack;
			return $this->arPacket;
		}
		##################################################################################################################
		public function compare(Packet $PacketForCompare) //Сравнивает массивы пакетов с конца 
		{
			if (count($this->PacketArray()) >= count($PacketForCompare->PacketArray()))
			{
				$longArray = $this->PacketArray();
				$shortArray = $PacketForCompare->PacketArray();
			}
			else 
			{
				$longArray = $PacketForCompare->PacketArray();
				$shortArray = $this->PacketArray();
			}
			
			end($longArray);
			end($shortArray);
			
			while(current($longArray) !== FALSE)
			{
				if(current($shortArray) === FALSE) //Достигнут элемент, с которого массив $shortArray заканчивается
				{
					$key = key($longArray);
					$tmp[$key] = current($longArray);
				}
				elseif (current($longArray) !== current($shortArray)) //Найден несоответствующий элемент
				{
					return FALSE;
				}
				
				$long = prev($longArray);
				$short = prev($shortArray);
			}
			
			ksort($tmp);
			
			return $tmp;
		}
		
		public function parse() 
		{
			$this->setResult("Protocol:\tUndefined");
			$this->setResult("Packet don't parsed, because I don't know logic", 'error');
		}
		
	}
	
	class RMAP extends Packet
	{
		function __construct($packStr)
		{
			parent::__construct($packStr);
			$this->setResult("Protocol:\tRMAP");
		}
		
		static $errTable = array(
		array('appl' => '111', 'error' => 'Command executed successfully'					),
		array('appl' => '111', 'error' => 'General error code'								),
		array('appl' => '111', 'error' => 'Unused RMAP Packet Type or Command Code'			),
		array('appl' => '111', 'error' => 'Invalid key'										),
		array('appl' => '101', 'error' => 'Invalid Data CRC'								),
		array('appl' => '111', 'error' => 'Early EOP'										),
		array('appl' => '111', 'error' => 'Too much data'									),
		array('appl' => '111', 'error' => 'EEP'												),
		array('appl' => '000', 'error' => 'Reserved'										),
		array('appl' => '101', 'error' => 'Verify buffer overrun'							),
		array('appl' => '111', 'error' => 'RMAP Command not implemented or not authorised'	),
		array('appl' => '001', 'error' => 'RMW Data Length error'							),
		array('appl' => '111', 'error' => 'Invalid Target Logical Address'					)
		);
		
		static $errCommand = array('0000', '0001', '0100', '0101', '0110');
		
		static $protocolID = '01';
		
		static $MapCodeTable = array(
		'TargetLogicalAddress'		=> 0,
		'ProtocolID'				=> 1,
		'Instruction'				=> 2,
		'Key'						=> 3,
		'Status'					=> 4,
		'ReplyAddress'				=> 5,
		'LogicalAddress'			=> 6,
		'TransactionIdentifier'		=> 7,
		'Reserved'					=> 8,
		'Address'					=> 9,
		'DataLength'				=> 10,
		'HeaderCRC'					=> 11,
		'Data'						=> 12,
		'Mask'						=> 13,
		'DataCRC'					=> 14
		); 
		
		

		/* Карта содержащит обязательные изначальные три байта. */
		private $packetMap = array(
		array('type' => 'TargetLogicalAddress',	'length' => 1),		// Target Logical Address
		array('type' => 'ProtocolID',			'length' => 1),		// Protocol ID
		array('type' => 'Instruction',			'length' => 1)		// Instruction
		);

		################################################Методы доступа################################################
		
		public function getMap($Decompressed = FALSE) //Возвращает карту пакета (в свернутом виде или побайтовом кодированном виде)
		{
			if ($Decompressed)
			{
				foreach ($this->packetMap as $arValue)
				{
					for ($i=0 ;$i < $arValue['length']; $i++)
					{
						$tmpMap[] = RMAP::$MapCodeTable[$arValue['type']];
					}
				}
				return $tmpMap;
			}
			else
				return $this->packetMap;
		}
		
		private function updateMap(array $arMap)
		{
			if (key($arMap) !== NULL)
				$this->packetMap = array_merge($this->packetMap, $arMap);
		}
		
		##############################################################################################################
		private function getPosition($pointer, $mode = 'number') //Getting position in Packet by Packet Map pointer
		{
			$arMap = $this->getMap();
			
			if ('type' === $mode)
			{
				foreach ($arMap as $i => $arValue)
				{
					if ($pointer === $arValue['type'])
					{
						$pointer = $i;
						break;
					}	
				}
			}
			
			for ($posInPacket = 0, $c = 0; $c < $pointer; $c++) //Calculate current byte position
			{
				$posInPacket += $arMap[$c]['length'];
			}
			
			return $posInPacket;
		}
		
		static public function calculateCRC (array $arData)
		{
			$RMAP_CRCTable = array(
					'00', '91', 'e3', '72', '07', '96', 'e4', '75', '0e', '9f', 'ed', '7c',
					'09', '98', 'ea', '7b', '1c', '8d', 'ff', '6e', '1b', '8a', 'f8', '69', '12', '83', 'f1', '60', '15',
					'84', 'f6', '67', '38', 'a9', 'db', '4a', '3f', 'ae', 'dc', '4d', '36', 'a7', 'd5', '44', '31', 'a0',
					'd2', '43', '24', 'b5', 'c7', '56', '23', 'b2', 'c0', '51', '2a', 'bb', 'c9', '58', '2d', 'bc', 'ce',
					'5f', '70', 'e1', '93', '02', '77', 'e6', '94', '05', '7e', 'ef', '9d', '0c', '79', 'e8', '9a', '0b',
					'6c', 'fd', '8f', '1e', '6b', 'fa', '88', '19', '62', 'f3', '81', '10', '65', 'f4', '86', '17', '48',
					'd9', 'ab', '3a', '4f', 'de', 'ac', '3d', '46', 'd7', 'a5', '34', '41', 'd0', 'a2', '33', '54', 'c5',
					'b7', '26', '53', 'c2', 'b0', '21', '5a', 'cb', 'b9', '28', '5d', 'cc', 'be', '2f', 'e0', '71', '03',
					'92', 'e7', '76', '04', '95', 'ee', '7f', '0d', '9c', 'e9', '78', '0a', '9b', 'fc', '6d', '1f', '8e',
					'fb', '6a', '18', '89', 'f2', '63', '11', '80', 'f5', '64', '16', '87', 'd8', '49', '3b', 'aa', 'df',
					'4e', '3c', 'ad', 'd6', '47', '35', 'a4', 'd1', '40', '32', 'a3', 'c4', '55', '27', 'b6', 'c3', '52',
					'20', 'b1', 'ca', '5b', '29', 'b8', 'cd', '5c', '2e', 'bf', '90', '01', '73', 'e2', '97', '06', '74',
					'e5', '9e', '0f', '7d', 'ec', '99', '08', '7a', 'eb', '8c', '1d', '6f', 'fe', '8b', '1a', '68', 'f9',
					'82', '13', '61', 'f0', '85', '14', '66', 'f7', 'a8', '39', '4b', 'da', 'af', '3e', '4c', 'dd', 'a6',
					'37', '45', 'd4', 'a1', '30', '42', 'd3', 'b4', '25', '57', 'c6', 'b3', '22', '50', 'c1', 'ba', '2b',
					'59', 'c8', 'bd', '2c', '5e', 'cf' );
				
			$crc = 0;
			
			foreach ($arData as $i => $value)
			{
				$arData[$i] = (int)base_convert($arData[$i], 16, 10);
				$crc = (int)base_convert($crc, 16, 10);
				
				$crc = $RMAP_CRCTable[$crc ^ $arData[$i]];
			}
			
			return $crc;
		}
		
		public function parse($posInMap = 0)
		{
			###################################################################################################################################################
			######################################################Рекурсивный алгоритм пробега по массиву######################################################
			/*	Рекурсивный алгоритм. Шагаем по массиву карты и выполняем обработчики, в которые передаем в качестве параметра значение или массив значений самого пакета,
			 в зависимости от значения поля length соответствующего элемента карты. Алгоритм выполняется пока карта не кончится. В процессе выполнения каждый обработчик
			 добавляет к массиву карты свой кусок. Неустановленные обработчики игнорируются.	*/
			$arPacket = $this->PacketArray();
			$arMap = $this->getMap();
			$handlerName = 'Parse' . $arMap[$posInMap]['type'];
			$posInPacket = $this->getPosition($posInMap);
			
			if ($arMap[$posInMap] !== NULL) //Если это условие выполняется, то мы все ещё в пределах карты
			{
				if (is_callable(array($this, $handlerName)))
				{
					$arCurrentPacketBytes = ($arMap[$posInMap]['length'] > 1) ? array_slice($arPacket, $posInPacket, $arMap[$posInMap]['length']) : $arPacket[$posInPacket];

					//Если слишком много данных, вызовется функция без параметра.
					//Это приведет к анализу отсутствующих данных.
					call_user_func(array($this, $handlerName), $arCurrentPacketBytes); 
				}

				return $this->parse($posInMap + 1);
			}
			
			###################################################################################################################################################
			###################################################################################################################################################

			if ($arMap) //Если $arMap = NULL, то уже была обнаружена другая ошибка и проверять окончание бесмысленно
			{
				if ($posInPacket > count($arPacket))
				{
					$this->setResult(RMAP::$errTable[5]['error'], 'error'); //Слишком много данных
					unset($this->packetMap);
				}
				elseif ($posInPacket < count($arPacket))
				{
					$this->setResult(RMAP::$errTable[6]['error'], 'error'); //Раннее завершение передачи
					unset($this->packetMap);
				}
			}
			
			return $this->getResult();
		}
		
		##################################################Функции детального анализа##################################################		
		private function ParseInstruction($instrByte) 
		{
			$instrBin = sprintf("%08d", base_convert($instrByte, 16, 2));
			
			$arMap; //Часть карты пакета
			
			$command = $instrBin[2] . $instrBin[3] . $instrBin[4] . $instrBin[5];
			$error = array_search($command, RMAP::$errCommand); //Поиск ошибки команды в массиве ошибок
			
			if ($instrBin[0]) //Если резервный бит равен единице - дальнейший анализ бессмысленен
			{
				$this->setResult(RMAP::$errTable[2]['error'], 'error');
				unset($this->packetMap);
			}
			elseif ($instrBin[1] && ($error !== FALSE)) //Если это пакет команды и обнаружена ошибка в блоке команды - дальнейший анализ бессмысленен
			{
				$this->setResult(RMAP::$errTable[2]['error'], 'error');
				unset($this->packetMap);
			}
			else 
			{
				#########################################################
				#####################Start create Map####################
				
				if ($instrBin[1]) //Command Packet
				{
					$this->setResult("Packet type:\tCommand packet");
					
					################Parse command bits###############
					/* Парсим только в случае, если командный пакет потому что для пакета ответа это бессмысленно (целостность и так проверяется по CRC,
					а сам блок представляет из себя копию аналогичного отправленного пакета команды) */
					
					if ($command == '0111') //Уникальный случай: команда Read-Modify-Write (можно отключить, не влияет ни на что)
						$this->setResult("Packet type:\tRead-Modify-Write");
					else
					{
						$instrBin[2] == 0 ? $this->setResult("Read", 'command')			: $this->setResult("Write", 'command');
						$instrBin[3] == 0 ? $this->setResult("Don't verify", 'command')	: $this->setResult("Verify data", 'command');
						$instrBin[4] == 0 ? $this->setResult("No reply", 'command')		: $this->setResult("Reply", 'command');
						$instrBin[5] == 0 ? $this->setResult("No inrement", 'command')	: $this->setResult("Increment", 'command');
					}
					#################################################

					$arMap[] = array('type' => 'Key', 'length' => 1); //Key
						
					switch($instrBin[6] . $instrBin[7]) //Считываем количество байт адреса ответа
					{
						case '00':
							$len = 0;
							break;
						case '01':
							$len = 4;
							break;
						case '10':
							$len = 8;
							break;
						case '11':
							$len = 12;
							break;
					}
						
					if ($len != 0)
					{
						$arMap[] = array('type' => 'ReplyAddress', 'length' => $len); //Reply Address
						$this->setResult("Reply address length:\t$len");
					}
				}
				else //Reply Packet
				{
					$arMap[] = array('type' => 'Status', 'length' => 1); //Status
					$this->setResult("Packet type:\tReply packet");
				}
				#######################Common############################
				
				$arMap[] = array('type' => 'LogicalAddress', 'length' => 1); //Logical Address (отправителя)
				$arMap[] = array('type' => 'TransactionID', 'length' => 2); //Transaction Identifier
				
				#########################################################
				if ($instrBin[1]) //Пакет команды
				{
					$arMap[] = array('type' => 'ExtendedAddress', 'length' => 1); //Extended Address
					$arMap[] = array('type' => 'Address', 'length' => 4); //Address
				}
				else //Пакет ответа
				{
					if ($instrBin[2]) //Пакет ответа на команду ЗАПИСИ (имеет короткое окончание)
					{
						$arMap[] = array('type' => 'HeaderCRC', 'length' => 1); //Header CRC
						$this->updateMap($arMap);
						return;
					}
					else
					{
						$arMap[] = array('type' => 'Reserved', 'length' => 1); //Reserved = 0
					}
				}
				#######################Common############################
				
				$arMap[] = array('type' => 'DataLength', 'length' => 3); //Data Length
				$arMap[] = array('type' => 'HeaderCRC', 'length' => 1); //Header CRC
				
				####################End create Map#######################
				#########################################################
				
				$this->updateMap($arMap);
			}
		}
		
		private function ParseStatus($statByte)
		{
			$statDec = base_convert($statByte, 16, 10);
			$arStatus = RMAP::$errTable[$statDec]; //Ищем статус по таблице
			isset($arStatus) ? $this->setResult("Status:\t{$arStatus['error']}") : $this->setResult("Status:\tStatus not defined");
		}
		
		private function ParseDataLength(array $arDataLengthBytes) //Выяснить правильность составления числа!!
		{
			//Не сделан анализ маски для частного случае RMW
			foreach ($arDataLengthBytes as $value) //Правильно ли составлено результирующее hex число байтов? Составляется простой конкатенацией
			{
				$dataLength .= $value;
			}
			$dataLength = (int)base_convert($dataLength, 16, 10);
			
			if ($dataLength != 0)
			{
				$arMap[] = array('type' => 'Data', 'length' => $dataLength);	//Data
				$this->setResult("Data length:\t$dataLength");
			}
			
			$arMap[] = array('type' => 'DataCRC', 'length' => 1);			//Data CRC
			
			$this->updateMap($arMap);
		}
		
		private function ParseHeaderCRC($crcByte)
		{	
			
			$end = $this->getPosition('HeaderCRC', 'type');
			$headerBytes = array_slice($this->PacketArray(), 0, $end);
			$crc = $this->calculateCRC($headerBytes);
			
			if ($crc !== $crcByte)
			{
				$this->setResult('Incorrect Header CRC', 'error');
				unset($this->packetMap);
			}
		}
		
		private function ParseData($arDataBytes) //Наполняет массив результатов байтами данных
		{
			if (is_array($arDataBytes))
			{
				foreach($arDataBytes as $value)
				{
					$this->setResult($value, 'data');
				}
			}
			else //Если один байт
				$this->setResult($arDataBytes, 'data');
		}
		
		private function ParseDataCRC($crcByte)
		{
				
			$end = $this->getPosition('DataCRC', 'type');
			$dataBytes = array_slice($this->PacketArray(), 0, $end);
			$crc = $this->calculateCRC($dataBytes);
				
			if ($crc !== $crcByte)
			{
				$this->setResult(RMAP::$errTable[4]['error'], 'error');
				unset($this->packetMap);
			}
		}
	}
	
	//error_reporting(E_ALL);
	//header('Content-Type: text/html; charset=utf-8');
	
	####################Правильные пакеты####################
	//Пакеты команды
	
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56'; //Пакет записи с данными
	
	
	
	//Пакеты ответа
	//$testPack = 'fe 01 38 00 67 00 a0 CRC'; //Ответ на команду записи
	###################НЕПРАВИЛЬНЫЕ пакеты###################
	//Пакеты команды
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 aa 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56'; //Пакет записи. Invalid Header CRC.
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb'; //Пакет записи. Invalid Data CRC.
	
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 01 9f 01 bb'; //Пакет записи, с 1 байтом данных. Invalid Data CRC
	
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56 ff ff ff ff'; //Пакет записи с данными. Too much data
	//$testPack = 'fe 01 6c 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Early EOP
	//$testPack = 'fe 01 80 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Использован зарезервированный бит в блоке команды.
	//$testPack = 'fe 01 44 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Неправильное значение блока команды.
	//Пакеты ответа
	//$testPack = 'fe 01 38 00 67 00 a0'; //Ответ на команду записи. Early EOP
	#########################################################
	
	//$foo = Packet::Factory($testPack);
	//var_dump($foo->parse());
	
	//$foo->showResult();
	
	/* $first = '11 22 33 44 55 66 77 ff fg fe dr fg hg fd';
	$second = '11 22 33 44 55 66 77 ff fg fe dr fg hg fd';
	//$second =					  'ff DF fe dr fg hg fd';
	//$second =					  'ff fg fe dr fg hg fd';
	
	$foo = Packet::Factory($first);
	$bar = Packet::Factory($second);
	
	var_dump($foo->compare($bar)); */
	
	
?>