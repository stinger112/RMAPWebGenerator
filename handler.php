<?php
	require_once 'parser.php';
	
	header('Content-Type: text/html; charset=utf-8');
	error_reporting(0);

	if (isset($_GET['generate']))
	{
		if ($_POST['ADDRESS'])
			$packetStr .= $_POST['ADDRESS'] . " ";
		
		if ($_POST['HEADER'])
			$packetStr .= $_POST['HEADER'] . " ";
		
		if ($_POST['DATA'])
			$packetStr .= $_POST['DATA'];
		
		$included_pack = trim($packetStr);
		include 'forms/pack_parse_form.inc';
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
	}
	
	else
	{
		header('Location: index.php');
	}
		
?>