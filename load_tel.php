<?php
	if(isset($_GET['client']) && !empty($_GET['client']))
	{
		$xmlfile = 'https://www.annonces-automobile.com/script/xml/client.php?key=aA$Xm1&client='.$_GET['client'];
		$xml = simplexml_load_file($xmlfile);
		
		$jsont = array();

		$jsont[] = strval($xml->tel1);
		
		echo $_GET['callback'].'('.json_encode($jsont).');';
	}
?>