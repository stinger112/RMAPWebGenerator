<?php
	require_once 'parser.php';
	error_reporting(E_ALL & ~E_NOTICE);
	header('Content-Type: text/html; charset=utf-8');
	
	####################Правильные пакеты####################
	//Пакеты команды
	$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56'; //Пакет записи с данными
	//$testPack = 'fe 01 4c 00 67 00 01 00 a0 00 00 00 00 00 10 c9';
	//$testPack = 'fe 01 6e 00 00 99 aa bb cc dd ee 00 67 00 02 00 a0 00 00 10 00 00 10 7f a0 a1 a2 a3 a4 a5 a6 a7 a8 a9 aa ab ac ad ae af b4';
	//$testPack = 'fe 01 4d 00 99 aa bb cc 67 00 03 00 a0 00 00 10 00 00 10 f7';
	//Пакеты ответа
	//$testPack = 'fe 01 38 00 67 00 a0 74'; //Ответ на команду записи
	###################НЕПРАВИЛЬНЫЕ пакеты###################
	//Пакеты команды
	$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 ff 56';
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 aa 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56'; //Пакет записи. Invalid Header CRC.
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 bb'; //Пакет записи. Invalid Data CRC.
	
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 01 aa 01 bb'; //Пакет записи, с 1 байтом данных. Invalid Header and Data CRC
	
	//$testPack = 'fe 01 6c 00 67 00 00 00 a0 00 00 00 00 00 10 9f 01 23 45 67 89 ab cd ef 10 11 12 13 14 15 16 17 56 ff ff ff ff'; //Пакет записи с данными. Too much data
	//$testPack = 'fe 01 6c 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Early EOP
	//$testPack = 'fe 01 80 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Использован зарезервированный бит в блоке команды.
	//$testPack = 'fe 01 44 00 67 00 a0 00 00 00 00 00'; //Пакет записи. Неправильное значение блока команды.
	//Пакеты ответа
	//$testPack = 'fe 01 38 00 67 00 a0'; //Ответ на команду записи. Early EOP
	#########################################################
	
	$foo = Packet::Factory($testPack)->parse();	
	echo "<p>Packet: $testPack</p>";
	//var_dump($foo->getResult());
	$foo->showResult();
	var_dump($foo->getMap('decoded'));
	
	/* $first = '11 22 33 44 55 66 77 ff fg fe dr fg hg fd';
	$second = '11 22 33 44 55 66 77 ff fg fe dr fg hg fd';
	//$second =					  'ff DF fe dr fg hg fd';
	//$second =					  'ff fg fe dr fg hg fd';
	
	$foo = Packet::Factory($first);
	$bar = Packet::Factory($second);
	
	var_dump($foo->compare($bar)); */
?>