<?php
	header('Content-Type: text/html; charset=utf-8');
	error_reporting(0);
	
	define(BIN, '0b');
	define(OCT, '0o');
	define(DEC, '0d');
	define(HEX, '0x');
	
	##Формирование строки оригинального пакета из формы##
	
	foreach ($_POST as $key => $value)
	{
		if (substr_count($key, "instr"))
			$instr .= $value;
		elseif (substr_count($key, "opt"))
			$arOptions["$key"] = $value;
		else
			$original_pack .= $key . ":\t" . $value . "\n";
	}
	
	$original_pack .= "Instr:\t" . BIN . $instr . "\t" . HEX . base_convert($instr, 2, 16) . "\n";
	#####################################################
	
	if (isset($_GET['gen']))
	{
		include 'forms/pack_gen_form.inc';
	}
	elseif (isset($_GET['compare']))
	{	

	}
	else
		include 'forms/pack_compare_form.inc';
?>