<?php
	// Chargement des options d'un select "modele"
	if(isset($_GET['currentmarque']) && !empty($_GET['currentmarque']))
	{
		$tabmodele = '';
		$currentmarque = $_GET['currentmarque'];

		$xmlfile = 'https://www.annonces-automobile.com/script/xml/series.php?key=aA$Xm1&marque='.$_GET['currentmarque'];
		if(isset($_GET['all']) && !empty($_GET['all'])){ $xmlfile .= '&all='.$_GET['all']; }
		if(isset($_GET['client']) && !empty($_GET['client'])){ $xmlfile .= '&client='.$_GET['client']; }
		$xml = simplexml_load_file($xmlfile);
		
		$jsont = array();
		
		foreach($xml->serie as $s){ array_push($jsont, $s->id.'|'.$s->nom); }
		
		echo $_GET['callback'].'('.json_encode($jsont).');';
	}
?>