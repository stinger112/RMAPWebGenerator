<?php 
	class Packet //!!Все внутренние массивы хранят hex-значения!!
	{	
		private  $arResult; //Хранит массив строк-сведений о пакете
		private $packetString; //Хранит пакет в строковом представлении
		private $packetArray; //Хранит побайтовый массив пакета
		
		protected function __construct($packStr)
		{
			$this->PacketString($packStr);
			
			$arPack = explode(" ", $packStr);
			$this->PacketArray($arPack);
		}
		
		static public function Factory($packStr, $addrLenInBytes = 0) //Метод-фабрика
		{
			if ($packStr && is_string($packStr))
			{
				
				$arPacket = explode(" ", $packStr);
				
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
		
		protected function PacketString($packStr)
		{
			if ($packStr) 
				$this->packetString = $packStr;
			return $this->packetString;
		}
		
		protected function PacketArray($arPack = NULL)
		{
			if ($arPack)
				$this->packetArray = $arPack;
			return $this->packetArray;
		}
		##################################################################################################################
		public function compare(Packet $PacketForCompare)
		{
			
		}
		
		public function parse() 
		{
			$this->setResult("Packet don't parsed, because I don't know logic");
			$this->setResult("Protocol:\tUndefined");
		}
		
	}
	
	class RMAP extends Packet
	{
		function __construct($packStr)
		{
			parent::__construct($packStr);
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
		
		static $packetMapCodeTable = array(
		0 =>	'No handler',
		1 =>	'Instruction',
				'Key',
		4 =>	'Status',
				'Initiator Logical Adress',
		2 =>	'Data Length',
		3 =>	'Header CRC',
		5 =>	'Data'
		);

		private $packetMap = array(					//Карта содержащит обязательные изначальные три байта. 
		array('type' => 0, 'length' => 1),		// Target Logical Address
		array('type' => 0, 'length' => 1),		// Protocol ID
		array('type' => 1, 'length' => 1)		// Instruction
		);

		################################################Методы доступа################################################
		
		public function getMap($Decompressed = FALSE) //Возвращает карту пакета (в свернутом виде или побайтовом виде)
		{
			if ($Decompressed)
			{
				foreach ($this->packetMap as $arValue)
				{
					for ($i=0 ;$i < $arValue['length']; $i++)
					{
						$tmpMap[] = $arValue['type'];
					}
				}
				return $tmpMap;
			}
			else
				return $this->packetMap;
		}
		
		private function updateMap($arMap) //Обновляет карту в соответствии с текущей маской
		{
			if (key($arMap) !== NULL)
				$this->packetMap = array_merge($this->packetMap, $arMap);
		}
		
		##############################################################################################################
		
		public function parse($posInMap = 0) //Рекурсивный парсер. Работает с отрезками развернутой карты, содержащими ненулевые значения.
		{
			$arPacket = $this->PacketArray();
			
			$arMap = $this->getMap(); //Получаем текущую карту
			
			$handlerName; //Имя обработчика (можно напрямую брать из $arMap)
			
			for ($posInPacket = 0, $c = 0; $c < $posInMap; $c++) //Считаем текущую позицию байта в пакете
			{
				$posInPacket += $arMap[$c]['length'];
			}

			echo "Position in map: $posInMap<br>";
			echo "Position in packet: $posInPacket<br>";
			
			//var_dump($arMap[$posInMap]['type']);
			
			switch(TRUE) //Дешифровка типа
			{
				case ($arMap[$posInMap]['type'] === 1): //Обрабатываем байт инструкции
					$handlerName = 'Instruction';
					break;
					
				case ($arMap[$posInMap]['type'] === 2): //Обрабатываем 3 байта описывающих длину данных
					$handlerName = 'DataLength';
					break;
					
				case ($arMap[$posInMap]['type'] === 5): //Обрабатываем байт данных
					$handlerName = 'Data';
					break;
					
				case ($arMap[$posInMap]['type'] === 4): //Обрабатываем байт статуса
					$handlerName = 'Data';
					break;
					
				case ($arMap[$posInMap]['type'] === 0): 
					$handlerName = 'Undefined';
					break;
					
				default:
					$handlerName = 'Exit';
					break;
			}

			//var_dump($arMap);
			
			if (is_callable(array($this, "Parse$handlerName")))
			{
				$handlerName = "Parse$handlerName";
				
				if ($arMap[$posInMap]['length'] > 1)
					$arCurrentPacketBytes = array_slice($arPacket, $posInPacket, $arMap[$posInMap]['length']); //Выделяем байты пакета от начала и до конца текущей позиции карты
				else
					$arCurrentPacketBytes = $arPacket[$posInPacket];
				
				echo $handlerName . "<br>";
				call_user_func(array($this, $handlerName), $arCurrentPacketBytes); //Если слишком много данных, вызовется функция без параметра. К чему это приведет - неясно
				
				return $this->parse($posInMap + 1);
			}
			elseif ($handlerName === 'Undefined') //Если обработчик неопределен - переходим на следующую позицию $arMap (можно сделать метод compressMap, который будет удалять необр. значения)
				return $this->parse($posInMap + 1);
			
			$this->setResult("Packet length:\t" . count($arPacket));
			$this->setResult("Protocol:\tRMAP");
			
			echo "Packet pos: $posInPacket";
			
			if ($arMap) //Если $arMap = NULL, то уже была обнаружена другая ошибка
			{
				if ($posInPacket > count($arPacket)) //Слишком много данных
				{
					$this->setResult(RMAP::$errTable[5]['error'], 'error');
					unset($this->packetMap);
				}
				elseif ($posInPacket < count($arPacket)) //Раннее завершение передачи
				{
					$this->setResult(RMAP::$errTable[6]['error'], 'error');
					unset($this->packetMap);
				}
			}
			
			return $this->getResult();
		}
		
		##################################################Функции детального анализа##################################################
		private function ParseInstruction($instrByte) 
		{
			//Возможно стоит переделать коды типов с цифровых на обычные значения для понятности, с предусмотренной возможностью вызова колбека,
			//но и декомперссию проводить с кодами, а не словами.
			$instrBin = sprintf("%08d", base_convert($instrByte, 16, 2));
			
			$arMap; //Часть маски пакета (соотв. заголовочным байтам) которая наложится на "максимальную" структуру пакета, и образует тем самым карту текущего пакета
			
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

					$arMap[] = array('type' => 0, 'length' => 1); //Key
						
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
						$arMap[] = array('type' => 0, 'length' => $len); //Reply Address
						$this->setResult("Reply address length:\t$len");
					}
				}
				else //Reply Packet
				{
					$arMap[] = array('type' => 4, 'length' => 1); //Status
					$this->setResult("Packet type:\tReply packet");
				}
				#######################Common############################
				
				$arMap[] = array('type' => 0, 'length' => 1); //Logical Address (отправителя)
				$arMap[] = array('type' => 0, 'length' => 2); //Transaction Identifier
				
				#########################################################
				if ($instrBin[1]) //Пакет команды
				{
					$arMap[] = array('type' => 0, 'length' => 1); //Extended Address
					$arMap[] = array('type' => 0, 'length' => 4); //Address
				}
				else //Пакет ответа
				{
					if ($instrBin[2]) //Пакет ответа на команду ЗАПИСИ (имеет короткое окончание)
					{
						$arMap[] = array('type' => 3, 'length' => 1); //Header CRC
						$this->updateMap($arMap);
						return;
					}
					else
					{
						$arMap[] = array('type' => 0, 'length' => 1); //Reserved = 0
					}
				}
				#######################Common############################
				
				$arMap[] = array('type' => 2, 'length' => 3); //Data Length
				$arMap[] = array('type' => 3, 'length' => 1); //Header CRC
				
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
		
		private function ParseDataLength($arDataLengthBytes) //Выяснить правильность составления числа!!
		{
			//Не сделан анализ маски для частного случае RMW
			if (is_array($arDataLengthBytes))
			{
				foreach ($arDataLengthBytes as $value) //Правильно ли составлено результирующее hex число байтов? Составляется простой конкатенацией
				{
					$dataLength .= $value;
				}
				$dataLength = (int)base_convert($dataLength, 16, 10);
				
				if ($dataLength != 0)
				{
					$arMap[] = array('type' => 5, 'length' => $dataLength);	//Data
					$this->setResult("Data length:\t$dataLength");
				}
				
				$arMap[] = array('type' => 0, 'length' => 1);			//Data CRC
				
				$this->updateMap($arMap);
			}
		}
		
		private function HeaderCRC()
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
			
			$crc = '00';
			
			for ($i = 0; $i < $length; $i++)
			{
				$crc = $RMAP_CRCTable[($crc ^ $data[i]) & 'ff'];
			}
			return crc;
		}
		
		private function ParseData($arDataBytes) //Наполняет массив результатов байтами данных
		{
			foreach($arDataBytes as $value)
			{
				$this->setResult($value, 'data');
			}
		}
	}
	
	################################################################
	
	error_reporting(E_ALL);
	header('Content-Type: text/html; charset=utf-8');
	
	####################Правильные пакеты####################
	//Пакеты команды
	$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 aa 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb'; //Пакет записи с данными
	//$testPack = 'fe 01 6d 00 00 00 00 00 67 00 00 00 a0 00 00 00 00 00 10 aa 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb'; // Предыдущий пакет + адрес ответа (00 00 00 00)
	//Пакеты ответа
	//$testPack = 'fe 01 38 00 67 00 a0 CRC'; //Ответ на команду записи
	###################НЕПРАВИЛЬНЫЕ пакеты###################
	//Пакеты команды
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 aa 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb ff ff ff ff'; //Пакет записи с данными. Too much data
	//$testPack = 'fe 01 6c 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Early EOP
	//$testPack = 'fe 01 80 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Использован зарезервированный бит в блоке команды.
	//$testPack = 'fe 01 44 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Неправильное значение блока команды.
	//Пакеты ответа
	//$testPack = 'fe 01 38 00 67 00 a0'; //Ответ на команду записи. Early EOP
	#########################################################
	
	$test = Packet::Factory($testPack);
	var_dump($test->parse());
	$test->showResult();
	
?>