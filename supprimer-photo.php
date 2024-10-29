<?php
	session_start();
	
	if(isset($_GET['img']) && is_numeric($_GET['img']) && isset($_GET['id_client']) && !empty($_GET['id_client']))
	{
		$pluginpath = str_replace('\\', '/', dirname(__FILE__));
		$targetPath = str_replace('/wp-content/plugins/annonces-automobile', '/wp-content/uploads/annoncesautomobile/photos/', $pluginpath);
		
		if(file_exists($targetPath.'small/'.$_GET['id_client'].'/'.$_GET['img'].'.jpg'))
		{
			unlink($targetPath.'small/'.$_GET['id_client'].'/'.$_GET['img'].'.jpg');
		}
		if(file_exists($targetPath.'big/'.$_GET['id_client'].'/'.$_GET['img'].'.jpg'))
		{
			unlink($targetPath.'big/'.$_GET['id_client'].'/'.$_GET['img'].'.jpg');
		}
		
		echo $_GET['callback'].'();';
	}
?>