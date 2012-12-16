<?php
	require_once 'parser.php';
	
	$errPacketExample = [
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
	];
	
	if ($_POST['opt'] == "error")
	{
		foreach (RMAP::$errTable as $arValue)
		{
			if ($arValue['code'] != 0 && $arValue['code'] != 8)
				echo '<option value="'. $arValue['code'] .'">' . $arValue['error'] . "</option>\n";
		}
	
	}
	elseif (substr_count($_POST['opt'], "err_type"))
	{
		
	}
?>