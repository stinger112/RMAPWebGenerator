<?php

	class Packet //!!Все внутренние массивы хранят hex-значения!!
	{	
		private $arAddress;		//This array contain packet address (in hex)
		private $arPacket;		//This array contain original packet array (in hex)
		private $arResult;		//This array contain parse results
		private $arParseErrors;	//This array contain parse errors
		
		protected function __construct($arPacket, $arAddress = NULL)
		{
			$this->arPacket = $arPacket;
			
			if (isset($arAddress[0]))
				$this->arAddress = $arAddress;
			
			$this->setResult("Packet length:\t" . (count($this->arPacket) + count($this->arAddress)));
		}
		
		static public function Factory($packStr, $addressLength = 0) //Метод-фабрика (Производит дифференциацию пакетов по типам)
		{
			if ($packStr && is_string($packStr))
			{	
				$packStr = trim($packStr);
				$arTmp = explode(' ', $packStr);
				
				if ($addressLength)
				{
					$arAddress = array_slice($arTmp, 0 ,$addressLength);
					$arPacket = array_slice($arTmp, $addressLength);
				}
				else
					$arPacket = $arTmp;
				
				switch ($arPacket[1])
				{
					case RMAP::$protocolID:
						$packet = new RMAP($arPacket, $arAddress);
						break;
					default:
						$packet = new Packet($arPacket, $arAddress);
						break;
				}
				
				return $packet;
			}
			else 
				throw new RuntimeException("Packet didn't created (wrong string received)");
		}
		
		static public function PacketFormatter($packet, $base) //Packet formatter from hex (without prefix) to bin\oct\dec\hex (with prefix)
		{
			switch ($base)
			{
				case 2:
					$prefix = '0b';
					break;
				case 8:
					$prefix .= '0o';
					break;
				case 10:
					$prefix .= '0d';
					break;
				case 16:
					$prefix .= '0x';
					break;
				default:
					$base = 16;
					break;
			}
			
			if (is_string($packet))
			{
				$packet = explode(" ", trim($packet));
			}
			
			foreach ($packet as $value)
			{
				$arTmp[] = $prefix . base_convert($value, 16, $base);
			}
			
			$formatted = implode(' ', $arTmp);
			
			return $formatted;
		}
		##################################################Access Methods##################################################
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
			echo "<h3>Parse results:</h3>";
			foreach($this->getResult() as $key => $valueExt)
			{
				if (is_array($valueExt))
				{
					echo "<font color='blue'>$key: </font>";
					foreach ($valueExt as $valueInt)
					{
						echo "$valueInt ";
					}
				}
				else
					echo "$valueExt";
				echo '<br>';
			}
			if (is_array($this->getParseErrors()))
			{
				echo "<h3>Detected parse errors:</h3>";
				foreach ($this->getParseErrors() as $value)
				{
					echo "<font color='red'>$value<br></font>";
				}
			}
		}
				
		protected function PacketArray($arPack = NULL) //Universal access method for Packet array
		{
			if ($arPack)
				$this->arPacket = $arPack;
			return $this->arPacket;
		}
		
		protected function getParseErrors()
		{
			return $this->arParseErrors;
		}
		
		protected function addError($errString)
		{
			$this->arParseErrors[] = $errString;
		}
		
		public function getPacketString($base)
		{
			if (is_array($this->arAddress))
				$packet = array_merge($this->arAddress, $this->arPacket);
			else 
				$packet = $this->arPacket;
		
			return $this->PacketFormatter($packet, $base);
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
			return $this;
		}
		
	}
	
	class RMAP extends Packet
	{
		private $Instruction; //Instruction byte in binary formatted form
		
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
		
		/* static $MapCodeTable = array(
		'TargetLogicalAddress'		=> 0,
		'ProtocolID'				=> 1,
		'Instruction'				=> 2,
		'Key'						=> 3,
		'Status'					=> 4,
		'ReplyAddress'				=> 5,
		'LogicalAddress'			=> 6,
		'TransactionID'				=> 7,
		'Reserved'					=> 8,
		'ExtendedAddress'			=> 9,
		'Address'					=> 9,
		'DataLength'				=> 10,
		'HeaderCRC'					=> 11,
		'Data'						=> 12,
		'Mask'						=> 13,
		'DataCRC'					=> 14
		);  */
		
		/* Карта содержащит обязательные изначальные три байта. */
		private $packetMap = array(
		array('type' => 'TargetLogicalAddress',	'length' => 1),
		array('type' => 'ProtocolID',			'length' => 1),
		array('type' => 'Instruction',			'length' => 1)
		);

		###############################################Открытые методы################################################
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
		
		public function getMap($mode = NULL) //Return packet map (in compact or decoded view)
		{
			if ('decoded' === $mode)
			{
				$arPacket = $this->PacketArray();
				$pointer = 0;
				
				foreach ($this->packetMap as $arValue)
				{
					$tmp = array_slice($arPacket, $this->getPosition($pointer), $arValue['length']);
					$decodedMap[$arValue['type']] = implode(' ', $tmp);
					$pointer++;
				}
				
				return $decodedMap;
			}
			else
				return $this->packetMap;
		}
		###############################################Support methods################################################
		protected function __construct($arPacket, $arAddress = NULL)
		{
			parent::__construct($arPacket, $arAddress);
			$this->setResult("Protocol:\tRMAP");
		}
		
		private function updateMap(array $arMap)
		{
			if (key($arMap) !== NULL)
				$this->packetMap = array_merge($this->packetMap, $arMap);
		}
		
		private function getPosition($pointer, $mode = 'number') //Getting position in Packet by Packet Map pointer
		{
			$arMap = $this->packetMap;
				
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
		
		protected function addError($error) //Create error based on string or error number
		{
			if (is_int($error))
			{
				parent::addError(RMAP::$errTable[$error]['error']);
			}
			elseif (is_string($error))
			{
				parent::addError($error);
			}
		}
		#############################################Main parse method################################################
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
				if ($arPacket[$posInPacket] !== NULL) //Если мы все ещё читаем пакет
				{
					if (is_callable(array($this, $handlerName))) //Если есть обработчик на данный тип поля карты
					{
						$arCurrentPacketBytes = ($arMap[$posInMap]['length'] > 1) ? array_slice($arPacket, $posInPacket, $arMap[$posInMap]['length']) : $arPacket[$posInPacket];
						call_user_func(array($this, $handlerName), $arCurrentPacketBytes); 
					}
	
					return $this->parse($posInMap + 1);
				}
				else 
					$this->addError(5); //Early EOP
			}
			###################################################################################################################################################
			###################################################################################################################################################
			
			if ($posInPacket < count($arPacket)) //Check on "Too much data" error
			{
				$this->addError(6);
				$len = count($arPacket) - $posInPacket;
				$tmpMap[] = array('type' => 'ExcessBytes', 'length' => $len); //Excess Bytes
				$this->updateMap($tmpMap);
			}

			return $this;
		}
		
		############################################Detail parse methods##############################################		
		private function ParseInstruction($instrByte) 
		{
			$instrBin = sprintf("%08d", base_convert($instrByte, 16, 2));
			$this->Instruction = $instrBin;
			
			$arMap; //Часть карты пакета
			
			$command = $instrBin[2] . $instrBin[3] . $instrBin[4] . $instrBin[5];
			
			/* Проверка валидности блока команды */
			$error = array_search($command, RMAP::$errCommand); //Поиск ошибки команды в массиве ошибок		
			if ($instrBin[0] || ($instrBin[1] && ($error !== FALSE))) //Использован зарезервированный бит или неверное набор битов в блоке команды
			{
				$this->addError(2); //Wrong reserved bit
				return;
			}
			/*-----------------------------------*/
			
			#########################################################
			#####################Start create Map####################
			
			if ($instrBin[1]) //Command Packet
			{
				$this->setResult("Packet type:\tCommand packet");
				
				################Parse command bits###############
				/* Парсим только в случае, если командный пакет потому что для пакета ответа это бессмысленно (целостность и так проверяется по CRC,
				а сам блок представляет из себя копию аналогичного отправленного пакета команды) */
				if ($command == '0111') //Уникальный случай: команда Read-Modify-Write (ещё не включена опция обработки подобных пакетов)
				{
					$this->setResult("Packet type:\tRead-Modify-Write");
					$this->addError("Unfortunately this algorithm did't have complete support RMW Packets (therefore probable unexpected parse errors)");
				}
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
		
		private function ParseStatus($statByte)
		{
			$statDec = base_convert($statByte, 16, 10);
			$arStatus = RMAP::$errTable[$statDec]; //Ищем статус по таблице
			isset($arStatus) ? $this->setResult("Status:\t{$arStatus['error']}") : $this->setResult("Status:\tStatus not defined");
		}
		
		private function ParseDataLength(array $arDataLengthBytes)
		{
			//Не сделан анализ маски для частного случае RMW
			foreach ($arDataLengthBytes as $value) //Результирующее hex число байтов составляется простой конкатенацией
			{
				$dataLength .= $value;
			}
			$dataLength = (int)base_convert($dataLength, 16, 10);
			
			$result = $this->getResult();
			//var_dump($this->Instruction);
			if ('1' == $this->Instruction[2]) //Если пакет Read, то не создаем поля Data и DataCRC (согласно структуре) 
			{
				if ($dataLength != 0)
				{
					$arMap[] = array('type' => 'Data', 'length' => $dataLength);	//Data
					$this->setResult("Data length:\t$dataLength");
				}
				
				$arMap[] = array('type' => 'DataCRC', 'length' => 1);			//Data CRC
				$this->updateMap($arMap);
			}
		}
		
		private function ParseHeaderCRC($crcByte)
		{	
			
			$length = $this->getPosition('HeaderCRC', 'type');
			$headerBytes = array_slice($this->PacketArray(), 0, $length);
			$crc = $this->calculateCRC($headerBytes);
			
			if ($crc !== $crcByte)
				$this->addError('Invalid Header CRC');
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
			$start = $this->getPosition('Data', 'type');	
			$length = $this->getPosition('DataCRC', 'type') - $start;		
			$dataBytes = array_slice($this->PacketArray(), $start, $length);
			$crc = $this->calculateCRC($dataBytes);
			
			if ($crc !== $crcByte)
				$this->addError(4); //Incorrect Data CRC
		}
	}
?>