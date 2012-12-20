<?php
	require_once 'parser.php';
	
	header('Content-Type: text/html; charset=utf-8');
	error_reporting(0);
	
	define(BIN, '0b');
	define(OCT, '0o');
	define(DEC, '0d');
	define(HEX, '0x');
	define(ASCII, "'");

	##Формирование строки оригинального пакета из формы##
	
	/* foreach ($_POST as $key => $value)
	{
		if (substr_count($key, "options"))
			$arOptions["$key"] = $value;
		else
		{
			$arElementNameCat = explode(":", $key);
			if ($arElementNameCat[2]== "instr")
				$instr .= $value;
			else 
			{
				$arPacket[$arElementNameCat[0]] = array($value, $arElementNameCat);
			}
			
		}
	} */
	
	/* $arPacket['02'] = $instr;
	
	ksort($arPacket, SORT_NUMERIC);
	
	foreach ($arPacket as $arValue)
	{
		$original_pack .= $arValue[1][0] . ":\t" . $arValue[0] . "\n";
	}
	
	$original_pack .= "\n\n";
	
	foreach ($arPacket as $key => $value)
	{
		$original_pack .= $value . " ";
	} */

	//$original_pack .= "Instr:\t" . BIN . $instr . "\t" . HEX . base_convert($instr, 2, 16) . "\n";
	
	#####################################################
	
	if (isset($_GET['generate']))
	{
		var_dump($_POST);
		
		//include 'forms/pack_parse_form.inc';
		echo '<p><a href="index.php?generate" style="text-decoration: none"><input type="button" value="Back" /></a></p>';
	}
	elseif (isset($_GET['parse']))
	{
		if ($_POST['packet'])
		{
			$packetObj = Packet::Factory(trim($_POST['packet']));
			$packetObj->parse();
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