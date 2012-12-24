<?php
	require_once 'parser.php';
	
	header('Content-Type: text/html; charset=utf-8');
	error_reporting(E_ALL & ~E_NOTICE);
	
	//define(BIN, '0b');
	//define(OCT, '0o');
	//define(DEC, '0d');
	//define(HEX, '0x');
	##Формирование строки оригинального пакета из формы##
		
	#####################################################
	
	if (isset($_GET['generate']))
	{
		var_dump($_POST);
		
		foreach ($_POST as $key => $value)
		{
			if (preg_match('/^(\d+):.*/', $key, $number))
				$arDrawPacket[(int)$number[1]] = $value;
			
			elseif (substr_count($key, 'Instruction'))
				$instr .= $value;
		
		}
		
		$arDrawPacket[3] = base_convert($instr, 2, 16);
		ksort($arDrawPacket);
		$packetStr = implode(' ', $arDrawPacket);

		$packetObj = Packet::Factory(trim($packetStr))->parse();
		echo "Draw Packet: $packetStr<br>";
		echo "Packet: " . $packetObj->getPacketString($_POST['base']);
		
		$packetObj->showResult();
		
		echo '<p><a href="index.php?generate" style="text-decoration: none"><input type="button" value="Back" /></a></p>';
	}
	elseif (isset($_GET['parse']))
	{
		if ($_POST['packet'])
		{
			$packetObj = Packet::Factory(trim($_POST['packet']))->parse();
			$packetObj->showResult();
		}
		else
			echo "<h2>Wrong Packet Input</h2>";
		
		echo '<p><a href="index.php?parse" style="text-decoration: none"><input type="button" value="Back" /></a></p>';
	}
	elseif (isset($_GET['compare']))
	{
		if ($_POST['original_pack'] && $_POST['received_pack'])
		{
	
			$firstPacket = Packet::Factory(trim($_POST['original_pack']));
			$secondPacket = Packet::Factory(trim($_POST['received_pack']));
			
			$compareResult = $firstPacket->compare($secondPacket);
			
			if (is_array($compareResult))
			{
				echo "First diverging bytes: ";
				foreach ($compareResult as $value)
				{
					echo $value . ' ';
				}
			}
			elseif ($compareResult === FALSE)
				echo "<h2>Packets have divergence!</h2>";
			elseif ($compareResult === NULL)
				echo "<h2>Packets occurrence completely!</h2>";

		}
		else
			echo "<h2>Wrong Packets Input</h2>";
		
		echo '<p><a href="index.php?compare" style="text-decoration: none"><input type="button" value="Back" /></a></p>';
	}
	else
	{
		header('Location: index.php');
	}
		
?>