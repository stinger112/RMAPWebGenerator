<?php
	header('Content-Type: text/html; charset=utf-8');
	error_reporting(0);
	
	##Таблицы сравнений##
	define(BIN, '0b');
	define(OCT, '0o');
	define(DEC, '0d');
	define(HEX, '0x');
	define(ASCII, "'");
	
	$erCommand[] = '0000';
	$erCommand[] = '0001';
	$erCommand[] = '0100';
	$erCommand[] = '0101';
	$erCommand[] = '0110';
	#####################

	##Формирование строки оригинального пакета из формы##
	
	foreach ($_POST as $key => $value)
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
				$arPacket["{$arElementNameCat[0]}"] = [$value, $arElementNameCat];
			}
			
		}
	}
	$arPacket['02'] = $instr;
	
	ksort($arPacket, SORT_NUMERIC);
	
	foreach ($arPacket as $arValue)
	{
			$original_pack .= $arValue[1][0] . ":\t" . $arValue[0] . "\n";
	}
	
	$original_pack .= "\n\n";
	
	foreach ($arPacket as $key => $value)
	{
		$original_pack .= $value . " ";
	}
	
	//$original_pack .= "Instr:\t" . BIN . $instr . "\t" . HEX . base_convert($instr, 2, 16) . "\n";
	#####################################################
	
	if (isset($_GET['gen']))
		include 'forms/pack_gen_form.inc';
	else
		include 'forms/pack_compare_form.inc';
?>