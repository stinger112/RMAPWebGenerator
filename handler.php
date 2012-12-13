<?php
	header('Content-Type: text/html; charset=utf-8');
	error_reporting(0);
	
	##Таблицы сравнений##
	define(BIN, '0b');
	define(OCT, '0o');
	define(DEC, '0d');
	define(HEX, '0x');
	define(ASCII, "'");
	
	$erTABLE[] = array('code' => '00', 'appl' => '111', 'error' => 'Command executed successfully');
	$erTABLE[] = array('code' => '01', 'appl' => '111', 'error' => 'General error code');
	$erTABLE[] = array('code' => '02', 'appl' => '111', 'error' => 'Unused RMAP Packet Type or Command Code');
	$erTABLE[] = array('code' => '03', 'appl' => '111', 'error' => 'Invalid key');
	$erTABLE[] = array('code' => '04', 'appl' => '101', 'error' => 'Invalid Data CRC');
	$erTABLE[] = array('code' => '05', 'appl' => '111', 'error' => 'Early EOP');
	$erTABLE[] = array('code' => '06', 'appl' => '111', 'error' => 'Too much data');
	$erTABLE[] = array('code' => '07', 'appl' => '111', 'error' => 'EEP');
	$erTABLE[] = array('code' => '08', 'appl' => '000', 'error' => 'Reserved');
	$erTABLE[] = array('code' => '09', 'appl' => '101', 'error' => 'Verify buffer overrun');
	$erTABLE[] = array('code' => '10', 'appl' => '111', 'error' => 'RMAP Command not implemented or not authorised');
	$erTABLE[] = array('code' => '11', 'appl' => '001', 'error' => 'RMW Data Length error');
	$erTABLE[] = array('code' => '12', 'appl' => '111', 'error' => 'Invalid Target Logical Address');
	
	$erCommand[] = '0000';
	$erCommand[] = '0001';
	$erCommand[] = '0100';
	$erCommand[] = '0101';
	$erCommand[] = '0110';
	#####################
	
	/*foreach($erTABLE as $value)
	{
		echo "<p>". $value['code'] . " " . $value['appl'] . " " . $value['error'];
	}*/
	
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
		include 'forms/pack_gen_form.inc';
	else
		include 'forms/pack_compare_form.inc';
?>