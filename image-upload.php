<?php
	session_start();
	
	$pluginpath = str_replace('\\', '/', dirname(__FILE__));
	$targetPath = str_replace('/wp-content/plugins/annonces-automobile', '/wp-content/uploads/annoncesautomobile/photos/', $pluginpath);
	
	if(isset($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
	{
		if(!empty($_POST['id_client']))
		{
			if(!empty($_FILES) && $_FILES['image']['tmp_name']!="")
			{
				if(filesize($_FILES['image']['tmp_name'])<4194304)
				{
					$id_client = $_POST['id_client'];
					
					$tempFile = $_FILES['image']['tmp_name'];
					$fileTypes = array('jpg','jpeg','JPG','JPEG');
					$fileParts = pathinfo($_FILES['image']['name']);

					if(in_array($fileParts['extension'], $fileTypes))
					{
						if(!is_dir($targetPath.'big/'.$id_client)){ mkdir($targetPath.'big/'.$id_client, 0777); }
						if(!is_dir($targetPath.'small/'.$id_client)){ mkdir($targetPath.'small/'.$id_client, 0777); }

						if(move_uploaded_file($tempFile, $targetPath."big/".$id_client."/".$_POST['name'].".jpg"))
						{
							require $pluginpath.'/class-img.php';

							$img = new img($targetPath."big/".$id_client."/".$_POST['name'].".jpg");
							$img->resize(500,375);
							$img->store($targetPath."small/".$id_client."/".$_POST['name'].".jpg");
							unset($img);
							
							$img = new img($targetPath."big/".$id_client."/".$_POST['name'].".jpg");
							$img->resize(1200,900);
							$img->store($targetPath."big/".$id_client."/".$_POST['name'].".jpg");
							unset($img);
						}
						else
						{
							echo "Une erreur est survenue, vérifiez la taille et le format de votre image.";
						}
					}
					else
					{
						echo "L'image n'est pas valide.";
					}
				}
				else
				{
					echo "La taille de l'image est supérieure à 4 Mo.";
				}
			}
			else
			{
				echo "Une erreur est survenue, vérifiez la taille et le format de votre image.";
			}
		}
		else
		{
			echo "Utilisateur non connecté !";
		}
	}
?>