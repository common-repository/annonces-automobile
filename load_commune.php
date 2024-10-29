<?php
	if(isset($_GET['currentdepartement']) && !empty($_GET['currentdepartement']))
	{
		$currentdepartement = $_GET['currentdepartement'];

		$xmlfile = 'https://www.annonces-automobile.com/script/xml/communes.php?key=aA$Xm1&departement='.$currentdepartement;
		$xml = simplexml_load_file($xmlfile);
		
		$jsont = array();
		
		foreach($xml->commune as $c){ array_push($jsont, $c->id.'|'.$c->nom); }
		
		echo $_GET['callback'].'('.json_encode($jsont).');';
	}
?>