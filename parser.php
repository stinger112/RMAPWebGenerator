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
			
		}
		
		/* static $errTable = [
		['code' => '00', 'appl' => '111', 'error' => 'Command executed successfully'					],
		['code' => '01', 'appl' => '111', 'error' => 'General error code'								],
		['code' => '02', 'appl' => '111', 'error' => 'Unused RMAP Packet Type or Command Code'			],
		['code' => '03', 'appl' => '111', 'error' => 'Invalid key'										],
		['code' => '04', 'appl' => '101', 'error' => 'Invalid Data CRC'									],
		['code' => '05', 'appl' => '111', 'error' => 'Early EOP'										],
		['code' => '06', 'appl' => '111', 'error' => 'Too much data'									],
		['code' => '07', 'appl' => '111', 'error' => 'EEP'												],
		['code' => '08', 'appl' => '000', 'error' => 'Reserved'											],
		['code' => '09', 'appl' => '101', 'error' => 'Verify buffer overrun'							],
		['code' => '10', 'appl' => '111', 'error' => 'RMAP Command not implemented or not authorised'	],
		['code' => '11', 'appl' => '001', 'error' => 'RMW Data Length error'							],
		['code' => '12', 'appl' => '111', 'error' => 'Invalid Target Logical Address'					]
		]; */
		
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
		
		function parse()
		{
			
			$this->setResult("Protocol:\tRMAP");
			
			$tmp = $this->PacketArray();
			//var_dump($this->PacketArray());
			
			$this->ParseInstruction($tmp[2]);
			if (TRUE) //если это пакет ответа
				$this->ParseStatus($tmp[3]);
				
			return $this->getResult();
		}
		
		##################################################Функции детального анализа##################################################
		private function ParseInstruction($instrByte) //Изменить вывод на табличныйа не напрямую в результаты
		{
			//Добавить разбиение на 3 типа вместо одной строки?
			$instrBin = sprintf("%08d", base_convert($instrByte, 16, 2));
			
			$arPieceofMap = [0, 0]; //Логический адрес, протокол
			
			echo $instrBin . "<br>";
			
			if ($instrBin[0])
			{
				 $this->setResult("Error:\tPacket Type (invalid reserved bit)");
			}
			else 
			{
	
				$command = $instrBin[2] . $instrBin[3] . $instrBin[4] . $instrBin[5];
				$error = array_search($command, RMAP::$errCommand);
				
				if (is_int($error)) //Ошибочная команда
					$this->setResult("Error:\tCommand");
				else 
				{
					$instrBin[1] == 0 ? $this->setResult("Reply packet")	: $this->setResult("Command packet");
					$instrBin[2] == 0 ? $this->setResult("Read")			: $this->setResult("Write");
					$instrBin[3] == 0 ? $this->setResult("Don't verify")	: $this->setResult("Verify data");
					$instrBin[4] == 0 ? $this->setResult("No reply")		: $this->setResult("Reply");
					$instrBin[5] == 0 ? $this->setResult("No inrement")		: $this->setResult("Increment");
				}
			}
			
			$tmp = "Reply address length:\t";
			switch($instrBin[6] . $instrBin[7])
			{
				case '00':
					$this->setResult($tmp . "0");
					break;
				case '01':
					$this->setResult($tmp . "4");
					break;
				case '10':
					$this->setResult($tmp . "8");
					break;
				case '11':
					$this->setResult($tmp . "12");
					break;
			}
			
			return $arPieceofMap;
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
	//$testPack = 'fe 01 80 00 67 00 a0 00 00 00 00 00';
	
	$foo = Packet::Factory($testPack);
	var_dump($foo);
	var_dump($foo->parse());
	
	/* foreach(RMAP::$errTable as $value)
	{
		echo "<p>". $value['code'] . " " . $value['appl'] . " " . $value['error'];
	} */
	
?>