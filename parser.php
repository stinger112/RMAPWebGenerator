<?php 
	class Packet //!!Все внутренние массивы хранят hex-значения!!
	{	
		private $arResult; //Хранит массив строк-сведений о пакете
		private $packetString; //Хранит пакет в строковом представлении
		private $packetArray; //Хранит побайтовый массив пакета
		
		protected function __construct($packStr)
		{
			$this->PacketString($packStr);
			
			$arPack = explode(" ", $packStr);
			$this->PacketArray($arPack);
		}
		
		static public function Factory($packStr, $addrLenInBytes = 0)
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
						$packet = new UndefinedPacket;
						break;
				}
				
				return $packet;
			}
			else 
				throw new RuntimeException("Packet didn't created (wrong string received)");
		}
		
		##################################################Методы доступа##################################################
		protected function setResult($str)
		{
			$this->arResult[] = $str;
			return $this->arResult;
		}
		
		public function getResult()
		{
			return $this->arResult;
		}
		
		protected function PacketString($packStr)
		{
			$packStr != NULL ? $this->packetString = $packStr : NULL;
			return $this->packetString;
		}
		
		protected function PacketArray($arPack = NULL)
		{
			$arPack != NULL ? $this->packetArray = $arPack : NULL;
			return $this->packetArray;
		}
		##################################################################################################################
		
		
		public function parse() {}
		
	}
	
	class UndefinedPacket extends Packet
	{
		function __construct($packStr)
		{
			parent::__construct($packStr);
		}
		
		public function parse()
		{
			$this->setResult("Packet don't parsed, because I don't know logic");
		}
	}
	
	class RMAP extends Packet
	{
		function __construct($packStr)
		{
			parent::__construct($packStr);
			$this->setResult("Protocol:\tRMAP");
		}
		
		static $errTable = [
		['appl' => '111', 'error' => 'Command executed successfully'					],
		['appl' => '111', 'error' => 'General error code'								],
		['appl' => '111', 'error' => 'Unused RMAP Packet Type or Command Code'			],
		['appl' => '111', 'error' => 'Invalid key'										],
		['appl' => '101', 'error' => 'Invalid Data CRC'									],
		['appl' => '111', 'error' => 'Early EOP'										],
		['appl' => '111', 'error' => 'Too much data'									],
		['appl' => '111', 'error' => 'EEP'												],
		['appl' => '000', 'error' => 'Reserved'											],
		['appl' => '101', 'error' => 'Verify buffer overrun'							],
		['appl' => '111', 'error' => 'RMAP Command not implemented or not authorised'	],
		['appl' => '001', 'error' => 'RMW Data Length error'							],
		['appl' => '111', 'error' => 'Invalid Target Logical Address'					]
		];
		
		static $errCommand = ['0000', '0001', '0100', '0101', '0110'];
		
		static $protocolID = '01';
		
		static $packetMapCodeTable = [
		0 =>	'No handler',
		1 =>	'Instruction',
				'Key',
		4 =>	'Status',
				'Initiator Logical Adress',
		2 =>	'Data Length',
		3 =>	'Header CRC'
		];
		
		private $FullHeaderStructure = [ 			//Массив, содержащий МАКСИМАЛЬНОЕ число различных типов байтов заголовока. 
		['type' => 0, 'length' => 1],		// Target Logical Address
		['type' => 0, 'length' => 1],		// Protocol ID
		['type' => 1, 'length' => 1],		// Instruction
		['type' => 0, 'length' => 1],		// Key|Status
		['type' => 0, 'length' => 0],		// Reply Address
		['type' => 0, 'length' => 1],		// Initiator|Target Logical Address
		['type' => 0, 'length' => 2],		// Transaction ID
		['type' => 0, 'length' => 1],		// Extended Address|Reserved = 0(byte)
		['type' => 0, 'length' => 4],		// Address
		['type' => 2, 'length' => 3],		// Data Length
		['type' => 3, 'length' => 1]		// Header CRC
		];
		
		public function getMap($Decompressed = FALSE) //Возвращает карту пакета в свернутом виде
		{
			
			if ($Decompressed) //Возвращает развернутую карту пакета
			{
				foreach ($this->FullHeaderStructure as $arValue)
				{
					for ($i=0 ;$i < $arValue['length']; $i++)
					{
						$tmpMap[] = $arValue['type'];
					}
				}
				return $tmpMap;
			}
			else
				return $this->FullHeaderStructure;
		}
		
		
		public function parse($packetMap = NULL)
		{

			$arPacketBytes = $this->PacketArray();
			
			$tmpHeaderStructure = $this->getMap(TRUE);

			foreach ($tmpHeaderStructure as $arCurrentByteCode)
			{
				switch($arCurrentByteCode)
				{
					case 1: //Обрабатываем байт инструкции
						$this->ParseInstruction($arPacketBytes[$i]);
						$tmpHeaderStructure = $this->getMap(TRUE);
						break;
					case 4: //Обрабатываем байт статуса
						$this->ParseStatus($arPacketBytes[$i]);
						break;
					case 2:
						
						break;
					default:
						break;
				}
				$i++;
			}
			var_dump($tmpHeaderStructure);
			return $this->getResult();
		}
		
		##################################################Функции детального анализа##################################################
		private function ParseInstruction($instrByte) 
		{
			//Изменить вывод на табличныйа не напрямую в результаты
			//Добавить разбиение на 3 типа вместо одной строки?
			$instrBin = sprintf("%08d", base_convert($instrByte, 16, 2));
			
			
			
			if ($instrBin[0]) //Если резервный бит равен единице - дальнейший анализ бессмысленен
			{
				 $this->setResult("Error:\tPacket Type (invalid reserved bit)");
			}
			else 
			{
				###################Определяем тип пакета###################
				if ($instrBin[1]) //Пакет команды
				{
					$this->setResult("Command packet");
					
					switch($instrBin[6] . $instrBin[7]) //Если это пакет команды, то он может содержать байты адреса доставки. Считаем их количество.
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
					$this->FullHeaderStructure[4]['length'] = $len;
					$this->setResult("Reply address length:\t$len");
				}
				else //Пакет ответа
				{
					$this->setResult("Reply packet");
					
					$this->FullHeaderStructure[3]['type'] = 4; //Устанавливаем в 4 позицию поле Status 
					
					$this->FullHeaderStructure[4]['length'] = 0;
					
					if ($instrBin[2]) //Если это пакет ответа на команду записи, то он имеет короткий конец.
					{
						$this->setResult("Write");
						for ($i = 7; $i < 10; $i++) //Обнуляем все значения до Header CRC
						{
							$this->FullHeaderStructure[$i] = 0;
						}
					}
					else
						$this->setResult("Read");
				}
				##########################################################
	
				$command = $instrBin[2] . $instrBin[3] . $instrBin[4] . $instrBin[5];
				$error = array_search($command, RMAP::$errCommand);
				
				if (is_int($error)) //Ошибочная команда
					$this->setResult("Error:\tCommand");
				elseif ($command == '0111') //Команда Read-Modify-Write
					$this->setResult("Packet type:\tRead-Modify-Write");
				else
				{
					//$instrBin[1] == 0 ? $this->setResult("Reply packet")	: $this->setResult("Command packet");
					//$instrBin[2] == 0 ? $this->setResult("Read")			: $this->setResult("Write");
					$instrBin[3] == 0 ? $this->setResult("Don't verify")	: $this->setResult("Verify data");
					$instrBin[4] == 0 ? $this->setResult("No reply")		: $this->setResult("Reply");
					$instrBin[5] == 0 ? $this->setResult("No inrement")		: $this->setResult("Increment");
				}
			}
			
			//var_dump($this->FullHeaderStructure);
			foreach ($this->FullHeaderStructure as $arValue)
			{
				$tmp = "";
				$sumLength += (int)$arValue['length'];
				for ($i=0 ;$i < $arValue['length']; $i++)
				{
					$tmp .= $arValue['type'] . " ";
				}
				echo $tmp . "<br>";
			}
			echo "Length in bytes: " . $sumLength . "<br>";
			echo "Instruction byte: " . $instrBin . "<br>";
		}
		
		private function ParseStatus($statByte)
		{
			$tmp = "Status:\t";
			$statDec = base_convert($statByte, 16, 10);
			$arStatus = RMAP::$errTable[$statDec];
			isset($arStatus) ? $this->setResult($tmp . $arStatus["error"]) : $this->setResult($tmp . "Status not defined");
		}
		
		
		##############################################################################################################################
	}
	
	################################################################
	error_reporting(0);
	
	$testPack = 'fe 01 6c 00 67 00 a0 00 00 00 00 00';
	$testPack = 'fe 01 38 00 67 00 a0 CRC <EOP>'; // Ответ на команду записи
	
	$foo = Packet::Factory($testPack);
	var_dump($foo->parse());
	
?>