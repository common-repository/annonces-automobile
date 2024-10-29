<?php
/*
Plugin Name: Annonces Automobile
Plugin URI: https://www.annonces-automobile.com
Description: Affichez les annonces de voiture en provenance de la plateforme Annonces-Automobile.com
Author: Motorsgate
Version: 1.2
Author URI: https://www.motorsgate.com
*/

wp_register_style('annoncesautomobile_style', plugin_dir_url(__FILE__).'style.css');
wp_enqueue_style('annoncesautomobile_style');

add_action('wp_footer', 'annoncesautomobile_listing_js');
add_action('admin_menu', 'annoncesautomobile_admin_menu');
add_shortcode('annoncesautomobile_listing', 'annoncesautomobile_listing');
add_shortcode('annoncesautomobile_detail', 'annoncesautomobile_detail');
add_shortcode('annoncesautomobile_depot', 'annoncesautomobile_depot');

add_action('widgets_init', 'annoncesautomobile_recherche_register_widget');

add_filter('wpseo_canonical', '__return_false');
remove_action('wp_head', 'rel_canonical');
add_action('wp_head', 'annoncesautomobile_new_rel_canonical');

add_filter('wpseo_title', 'annoncesautomobile_custom_page_title', 15);
add_filter('pre_get_document_title', 'annoncesautomobile_custom_page_title');

function annoncesautomobile_wordpress_uploads_directory_path()
{
	$upload_dir = wp_upload_dir();
	return trailingslashit($upload_dir['basedir']);
}

function annoncesautomobile_wordpress_uploads_directory_url()
{
	$upload_dir = wp_upload_dir();
	return $upload_dir['baseurl'];
}

function annoncesautomobile_image($image)
{
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath.'images'))
	{
		mkdir($annoncesautomobilepath.'images', 0777, true);
	}
	
	if(!file_exists($annoncesautomobilepath.'images/'.basename($image)))
	{
		copy($image,$annoncesautomobilepath.'images/'.basename($image));
	}
	
	return annoncesautomobile_wordpress_uploads_directory_url().'/annoncesautomobile/images/'.basename($image);
}

function annoncesautomobile_custom_page_title()
{
	global $post;
	
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$tabchoice = explode('|', $annonceschoice);
	if(isset($tabchoice[0])){ $affichage = $tabchoice[0]; }
	if(isset($tabchoice[1])){ $affichageparam = $tabchoice[1]; }
	if(isset($tabchoice[2])){ $pagelisting = $tabchoice[2]; }
	if(isset($tabchoice[3])){ $pagedetail = $tabchoice[3]; }
	if(isset($tabchoice[4])){ $nbparpage = $tabchoice[4]; }
	
	if($pagedetail==$post->ID)
	{
		if(isset($_GET['annonce']))
		{
			$annonce = sanitize_text_field($_GET['annonce']);
			
			$xmlfile = 'https://www.annonces-automobile.com/script/xml/annonce.php?key=aA$Xm1&annonce='.$annonce;
			$xml = simplexml_load_file($xmlfile);
			
			$vehicule = htmlentities($xml->marque.' '.$xml->serie.' '.$xml->finition);
			
			return $vehicule.' - n°'.$annonce.' - '.get_bloginfo('name');
		}
	}
	else if($pagelisting==$post->ID)
	{
		if(isset($_GET['plisting']))
		{
			$plisting = sanitize_text_field($_GET['plisting']);
			
			return $post->post_title.' - Page '.$plisting.' - '.get_bloginfo('name');
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function annoncesautomobile_new_rel_canonical()
{
	global $post;
	
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$tabchoice = explode('|', $annonceschoice);
	if(isset($tabchoice[0])){ $affichage = $tabchoice[0]; }
	if(isset($tabchoice[1])){ $affichageparam = $tabchoice[1]; }
	if(isset($tabchoice[2])){ $pagelisting = $tabchoice[2]; }
	if(isset($tabchoice[3])){ $pagedetail = $tabchoice[3]; }
	if(isset($tabchoice[4])){ $nbparpage = $tabchoice[4]; }
	
	if($pagedetail==$post->ID)
	{
		if(isset($_GET['annonce']))
		{
			$annonce = sanitize_text_field($_GET['annonce']);
			
			$xmlfile = 'https://www.annonces-automobile.com/script/xml/annonce.php?key=aA$Xm1&annonce='.$annonce;
			$xml = simplexml_load_file($xmlfile);
			
			$vehicule = htmlentities($xml->marque.' '.$xml->serie.' '.$xml->finition);
		
			$link = 'https://www.annonces-automobile.com/acheter/voiture/'.annoncesautomobile_stripspecialchar($vehicule).'-'.$annonce;
			
			echo '<link rel="canonical" href="'.$link.'" />'."\n";
		}
	}
	else if($pagelisting==$post->ID)
	{
		$pos = strpos($_SERVER['REQUEST_URI'], '?');
		if($pos!==false){ echo '<meta name="robots" content="noindex, follow" />'."\n"; }
		
		echo '<link rel="canonical" href="'.wp_get_canonical_url($post->ID).'" />'."\n";
	}
	else
	{
		echo '<link rel="canonical" href="'.wp_get_canonical_url($post->ID).'" />'."\n";
	}
}

function annoncesautomobile_recherche_register_widget() {
	register_widget('annoncesautomobile_recherche_widget');
}

class annoncesautomobile_recherche_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'annoncesautomobile_recherche_widget',
			__('Annonces Automobile recherche', ' hstngr_widget_domain'),
			array( 'description' => __( 'Ajoutez un moteur de recherche pour vos annonces', 'hstngr_widget_domain' ), )
		);
	}
	public function widget( $args, $instance ) {
		$affichage = '';
		$affichageparam = '';
		$pagelisting = '';
		$pagedetail = '';
		
		$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
		if(!is_dir($annoncesautomobilepath))
		{
			mkdir($annoncesautomobilepath, 0777, true);
			$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
			fclose($listingfile);
		}
		
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
		$listingline = fgets($listingfile);
		if(!empty($listingline)){ $annonceschoice = $listingline; }
		fclose($listingfile);
		
		$tabchoice = explode('|', $annonceschoice);
		if(isset($tabchoice[0])){ $affichage = $tabchoice[0]; }
		if(isset($tabchoice[1])){ $affichageparam = $tabchoice[1]; }
		if(isset($tabchoice[2])){ $pagelisting = $tabchoice[2]; }
		if(isset($tabchoice[3])){ $pagedetail = $tabchoice[3]; }
		if(isset($tabchoice[4])){ $nbparpage = $tabchoice[4]; }
		
		$tmarque = array();
		$xmlfile = 'https://www.annonces-automobile.com/script/xml/marques.php?key=aA$Xm1';
		if($affichage=='marque'){ $xmlfile .= '&marque='.$affichageparam; }
		if($affichage=='client' && !empty($affichageparam)){ $xmlfile .= '&client='.$affichageparam; }
		$xml = simplexml_load_file($xmlfile);
		foreach($xml->marque as $m){ $tmarque[] = array($m->id, $m->nom); }
		
		$tmodele = array();
		if(isset($_GET['marque']))
		{
			$getmarque = sanitize_text_field($_GET['marque']);
			
			$xmlfile = 'https://www.annonces-automobile.com/script/xml/series.php?key=aA$Xm1&marque='.$getmarque;
			if($affichage=='client' && !empty($affichageparam)){ $xmlfile .= '&client='.$affichageparam; }
			$xml = simplexml_load_file($xmlfile);
			foreach($xml->serie as $m){ $tmodele[] = array($m->id, $m->nom); }
		}
		else if($affichage=='marque' && count(explode(',', $affichageparam))==1)
		{
			$xmlfile = 'https://www.annonces-automobile.com/script/xml/series.php?key=aA$Xm1&marque='.$affichageparam;
			$xml = simplexml_load_file($xmlfile);
			foreach($xml->serie as $m){ $tmodele[] = array($m->id, $m->nom); }
		}
		
		$tcategorie = array();
		$xmlfile = 'https://www.annonces-automobile.com/script/xml/categories.php?key=aA$Xm1';
		if($affichage=='categorie'){ $xmlfile .= '&categorie='.$affichageparam; }
		$xml = simplexml_load_file($xmlfile);
		foreach($xml->categorie as $c){ $tcategorie[] = array($c->id, $c->nom); }
		
		$tenergie = array();
		$xmlfile = 'https://www.annonces-automobile.com/script/xml/energies.php?key=aA$Xm1';
		$xml = simplexml_load_file($xmlfile);
		foreach($xml->energie as $e){ $tenergie[] = array($e->id, $e->nom); }
		
		echo '<section class="widget widget-annoncesautomobile">
				<form method="get" action="'.get_permalink($pagelisting).'">
					<div class="widget-title">
						<h2 class="box-titre">Rechercher une voiture</h2>
					</div>';
					if($affichage!='marque' || ($affichage=='marque' && count(explode(',', $affichageparam))>1))
					{
						echo '<select name="marque" id="marque" data-for="modele" class="selectmarque">
								<option value="" class="legende">Marque</option>';
								foreach($tmarque as $marque)
								{
									$selected = '';
									if(isset($getmarque) && $marque[0]==$getmarque){ $selected = ' selected="selected"'; }
									
									echo '<option value="'.$marque[0].'"'.$selected.'>'.$marque[1].'</option>';
								}
						echo '</select><br />';
					}
			echo '<select name="modele" id="modele">
						<option value="" class="legende">Modèle</option>';
						if(isset($getmarque) || ($affichage=='marque' && count(explode(',', $affichageparam))==1))
						{
							if(isset($_GET['modele'])){ $getmodele = sanitize_text_field($_GET['modele']); }
							
							foreach($tmodele as $modele)
							{
								$selected = '';
								if(isset($getmodele) && $modele[0]==$getmodele){ $selected = ' selected="selected"'; }
								
								echo '<option value="'.$modele[0].'"'.$selected.'>'.$modele[1].'</option>';
							}
						}
			echo '</select><br />';
			if($affichage!='categorie' || ($affichage=='categorie' && count(explode(',', $affichageparam))>1))
			{
				echo '<select name="categorie">
							<option value="" class="legende">Catégorie</option>';
							if(isset($_GET['categorie'])){ $getcategorie = sanitize_text_field($_GET['categorie']); }
							
							foreach($tcategorie as $categorie)
							{
								$selected = '';
								if(isset($getcategorie) && $categorie[0]==$getcategorie){ $selected = ' selected="selected"'; }
								
								echo '<option value="'.$categorie[0].'"'.$selected.'>'.$categorie[1].'</option>';
							}
				echo '</select><br />';
			}
			echo '<select name="energie">
						<option value="" class="legende">Energie</option>';
						if(isset($_GET['energie'])){ $getenergie = sanitize_text_field($_GET['energie']); }
						
						foreach($tenergie as $energie)
						{
							$selected = '';
							if(isset($getenergie) && $energie[0]==$getenergie){ $selected = ' selected="selected"'; }
							
							echo '<option value="'.$energie[0].'"'.$selected.'>'.$energie[1].'</option>';
						}
			echo '</select><br />
					<input type="text" name="prix_min" placeholder="Prix min (&euro;)" value="';
					if(isset($_GET['prix_min'])){ $getprix_min = sanitize_text_field($_GET['prix_min']); }
					if(isset($getprix_min)){ echo $getprix_min; }
					echo '" /><br />
					<input type="text" name="prix_max" placeholder="Prix max (&euro;)" value="';
					if(isset($_GET['prix_max'])){ $getprix_max = sanitize_text_field($_GET['prix_max']); }
					if(isset($getprix_max)){ echo $getprix_max; }
					echo '" /><br />
					<input type="text" name="km" placeholder="Kilométrage" value="';
					if(isset($_GET['km'])){ $getkm = sanitize_text_field($_GET['km']); }
					if(isset($getkm)){ echo $getkm; }
					echo '" /><br />
					<input class="button-green button-search" type="submit" value="Rechercher" />
				</form>
			</section>';
	}
	public function form($instance){}
	public function update($new_instance, $old_instance){}
}

function annoncesautomobile_admin_menu()
{
	add_menu_page('Annonces Automobile', 'Annonces Automobile', 'manage_options', 'annonces-automobile/admin.php', 'annoncesautomobile_admin_page', 'dashicons-performance', 6 );
}

function annoncesautomobile_admin_page()
{
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	
	$annonceschoice = 'all|';
	$affichage = '';
	$affichageparam = '';
	$pagelisting = '';
	$pagedetail = '';
	$nbparpage = 10;
	$couleur_listing_bordures = '#eaeaea';
	$couleur_listing_fond = '#ffffff';
	$couleur_listing_fond_hover = '#FFEBEB';
	$couleur_listing_titre = '#d40000';
	$couleur_listing_texte = '#333333';
	$couleur_listing_prix = '#d40000';
	$site_mode_listing = 'liste';
	$couleur_detail_prix = '#d40000';
	$couleur_detail_liste1 = '#FFFFFF';
	$couleur_detail_liste2 = '#EEEEEE';
	$slider_thumb = 'y';
	
	$tmarque = array();
	$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/marques.php?key=aA$Xm1');
	foreach($xml->marque as $m){ $tmarque[] = array($m->id, $m->nom); }
	
	$tcategorie = array();
	$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/categories.php?key=aA$Xm1');
	foreach($xml->categorie as $c){ $tcategorie[] = array($c->id, $c->nom); }
	
	if(isset($_POST['action'])){ $postaction = sanitize_text_field($_POST['action']); }
	if(isset($_POST['annonceschoice'])){ $postannonceschoice = sanitize_text_field($_POST['annonceschoice']); }
	
	if(isset($postaction) && $postaction=='annonces-automobile-param' &&
		isset($postannonceschoice))
	{
		$choice = 'all|';
		
		switch($postannonceschoice)
		{
			case 'all':
				$choice = 'all|';
			break;
			case 'marque':
				if(isset($_POST['marques'])){ $postmarques = array_map('sanitize_text_field', wp_unslash($_POST['marques'])); }
				if(isset($postmarques))
				{
					
					$choice = 'marque|'.implode(',',$postmarques);
				}
			break;
			case 'categorie':
				if(isset($_POST['categories'])){ $postcategories = array_map('sanitize_text_field', wp_unslash($_POST['categories'])); }
				if(isset($postcategories))
				{
					
					$choice = 'categorie|'.implode(',',$postcategories);
				}
			break;
			case 'client':
				if(isset($_POST['client'])){ $postclient = sanitize_text_field($_POST['client']); }
				if(isset($postclient))
				{
					$choice = 'client|'.$postclient;
				}
			break;
			default:
				$choice = 'all|';
			break;
		}
		
		if(isset($_POST['listing'])){ $postlisting = sanitize_text_field($_POST['listing']); }
		if(isset($_POST['detail'])){ $postdetail = sanitize_text_field($_POST['detail']); }
		if(isset($_POST['nbparpage'])){ $postnbparpage = sanitize_text_field($_POST['nbparpage']); }
		if(isset($_POST['depot'])){ $postdepot = sanitize_text_field($_POST['depot']); }
		
		if(isset($_POST['couleur_listing_bordures'])){ $postcouleur_listing_bordures = sanitize_text_field($_POST['couleur_listing_bordures']); }
		if(isset($_POST['couleur_listing_fond'])){ $postcouleur_listing_fond = sanitize_text_field($_POST['couleur_listing_fond']); }
		if(isset($_POST['couleur_listing_fond_hover'])){ $postcouleur_listing_fond_hover = sanitize_text_field($_POST['couleur_listing_fond_hover']); }
		if(isset($_POST['couleur_listing_titre'])){ $postcouleur_listing_titre = sanitize_text_field($_POST['couleur_listing_titre']); }
		if(isset($_POST['couleur_listing_texte'])){ $postcouleur_listing_texte = sanitize_text_field($_POST['couleur_listing_texte']); }
		if(isset($_POST['couleur_listing_prix'])){ $postcouleur_listing_prix = sanitize_text_field($_POST['couleur_listing_prix']); }
		
		if(isset($_POST['site_mode_listing'])){ $postsite_mode_listing = sanitize_text_field($_POST['site_mode_listing']); }
		
		if(isset($_POST['couleur_detail_prix'])){ $postcouleur_detail_prix = sanitize_text_field($_POST['couleur_detail_prix']); }
		if(isset($_POST['couleur_detail_liste1'])){ $postcouleur_detail_liste1 = sanitize_text_field($_POST['couleur_detail_liste1']); }
		if(isset($_POST['couleur_detail_liste2'])){ $postcouleur_detail_liste2 = sanitize_text_field($_POST['couleur_detail_liste2']); }
		
		if(isset($_POST['slider_thumb'])){ $postslider_thumb = sanitize_text_field($_POST['slider_thumb']); }
		
		$choice .= '|'.intval($postlisting).'|'.intval($postdetail).'|'.intval($postnbparpage).'|'.intval($postdepot).'|'.$postcouleur_listing_bordures.';'.$postcouleur_listing_fond.';'.$postcouleur_listing_fond_hover.';'.$postcouleur_listing_titre.';'.$postcouleur_listing_texte.';'.$postcouleur_listing_prix.'|'.$postsite_mode_listing.'|'.$postcouleur_detail_prix.';'.$postcouleur_detail_liste1.';'.$postcouleur_detail_liste2.'|'.$postslider_thumb;
		file_put_contents($annoncesautomobilepath.'listing.txt', $choice);
	}
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$tabchoice = explode('|', $annonceschoice);
	
	if(isset($tabchoice[0])){ $affichage = $tabchoice[0]; }
	if(isset($tabchoice[1])){ $affichageparam = $tabchoice[1]; }
	if(isset($tabchoice[2])){ $pagelisting = $tabchoice[2]; }
	if(isset($tabchoice[3])){ $pagedetail = $tabchoice[3]; }
	if(isset($tabchoice[4])){ $nbparpage = $tabchoice[4]; }
	if(isset($tabchoice[5])){ $pagedepot = $tabchoice[5]; }
	if(isset($tabchoice[6]))
	{
		$couleurs = explode(';', $tabchoice[6]);
		
		if(isset($couleurs[0]) && !empty($couleurs[0])){ $couleur_listing_bordures = $couleurs[0]; }
		if(isset($couleurs[1]) && !empty($couleurs[1])){ $couleur_listing_fond = $couleurs[1]; }
		if(isset($couleurs[2]) && !empty($couleurs[2])){ $couleur_listing_fond_hover = $couleurs[2]; }
		if(isset($couleurs[3]) && !empty($couleurs[3])){ $couleur_listing_titre = $couleurs[3]; }
		if(isset($couleurs[4]) && !empty($couleurs[4])){ $couleur_listing_texte = $couleurs[4]; }
		if(isset($couleurs[5]) && !empty($couleurs[5])){ $couleur_listing_prix = $couleurs[5]; }
	}
	if(isset($tabchoice[7])){ $site_mode_listing = $tabchoice[7]; }
	if(isset($tabchoice[8]))
	{
		$couleurs = explode(';', $tabchoice[8]);
		
		if(isset($couleurs[0]) && !empty($couleurs[0])){ $couleur_detail_prix = $couleurs[0]; }
		if(isset($couleurs[1]) && !empty($couleurs[1])){ $couleur_detail_liste1 = $couleurs[1]; }
		if(isset($couleurs[2]) && !empty($couleurs[2])){ $couleur_detail_liste2 = $couleurs[2]; }
	}
	if(isset($tabchoice[9])){ $slider_thumb = $tabchoice[9]; }
?>
	<div class="wrap">
		<h2>Administration Annonces Automobile</h2><br />
		<a href="https://www.annonces-automobile.com" target="_blank" rel="noopener"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/logo-annonces-automobile-black.png" alt="Annonces Automobile" width="248" height="57" /></a>
	</div>
	<div class="wrap">
		<form method="post" action="admin.php?page=annonces-automobile/admin.php">
			<input type="hidden" name="action" value="annonces-automobile-param" />
			<p>Affichez vos annonces sur vos pages par le biais du module Annonces-Automobile.com.</p>
			<hr />
			<h3>Affichage des annonces</h3>
			<p>Ci-dessous, choisissez les annonces à afficher en faisant votre choix. Par défaut toutes les annonces seront affichées.</p>
			<p><input type="radio" name="annonceschoice" value="all" id="annoncesautomobileall"<?php if($affichage=='all'){ echo ' checked="checked"'; } ?> /> <label for="annoncesautomobileall">Toutes les annonces</label></p>
			<p><input type="radio" name="annonceschoice" value="marque" id="annoncesautomobilemarque"<?php if($affichage=='marque'){ echo ' checked="checked"'; } ?> /> <label for="annoncesautomobilemarque">Marques</label> : <select name="marques[]" multiple>
				<?php
					foreach($tmarque as $marque)
					{
						$amarque = explode(',',$affichageparam);
						$selected = '';
						if(in_array($marque[0],$amarque) && $affichage=='marque'){ $selected = ' selected="selected"'; }
						
						echo '<option value="'.$marque[0].'"'.$selected.'>'.$marque[1].'</option>';
					}
				?>
			</select></p>
			<p><input type="radio" name="annonceschoice" value="categorie" id="annoncesautomobilecategorie"<?php if($affichage=='categorie'){ echo ' checked="checked"'; } ?> /> <label for="annoncesautomobilecategorie">Categories</label> : <select name="categories[]" multiple>
				<?php
					foreach($tcategorie as $categorie)
					{
						$acategorie = explode(',',$affichageparam);
						$selected = '';
						if(in_array($categorie[0],$acategorie) && $affichage=='categorie'){ $selected = ' selected="selected"'; }
						
						echo '<option value="'.$categorie[0].'"'.$selected.'>'.$categorie[1].'</option>';
					}
				?>
			</select></p>
			<p><input type="radio" name="annonceschoice" value="client" id="annoncesautomobileclient"<?php if($affichage=='client'){ echo ' checked="checked"'; } ?> /> <label for="annoncesautomobileclient">Identifiant client</label> : <input type="text" name="client" value="<?php if($affichage=='client'){ echo $affichageparam; } ?>" /></p>
			<hr />
			<h3>Page de listing</h3>
			<p>Créez votre page de listing en y insérant dans son contenu le tag ci-dessous.</p>
			<code>[annoncesautomobile_listing]</code>
			<p>Indiquez la page sur laquelle figure votre tag de listing.</p>
			<p>Page de listing : <select name="listing"><?php
				$pages = get_pages();
				foreach($pages as $page)
				{
					$selected = '';
					if($pagelisting==$page->ID){ $selected = ' selected="selected"'; }
					echo '<option value="'.$page->ID.'"'.$selected.'>'.$page->post_title.'</option>';
				}
			?></select></p>
			<p>Nombre d'annonces par page : <input type="text" name="nbparpage" value="<?php echo $nbparpage; ?>" /></p>
			<h4>Gestion des couleurs</h4>
			<table border="0">
				<tr>
					<td>Couleur de fond</td>
					<td>
						<input type="color" name="couleur_listing_fond" class="colorpicker" value="<?php echo $couleur_listing_fond; ?>" />
					</td>
					<td width="10"></td>
					<td>Couleur de fond actif</td>
					<td>
						<input type="color" name="couleur_listing_fond_hover" class="colorpicker" value="<?php echo $couleur_listing_fond_hover; ?>" />
					</td>
				</tr>
				<tr>
					<td>Couleur du titre</td>
					<td>
						<input type="color" name="couleur_listing_titre" class="colorpicker" value="<?php echo $couleur_listing_titre; ?>" />
					</td>
					<td width="10"></td>
					<td>Couleur du texte</td>
					<td>
						<input type="color" name="couleur_listing_texte" class="colorpicker" value="<?php echo $couleur_listing_texte; ?>" />
					</td>
				</tr>
				<tr>
					<td>Couleur du prix</td>
					<td>
						<input type="color" name="couleur_listing_prix" class="colorpicker" value="<?php echo $couleur_listing_prix; ?>" />
					</td>
					<td width="10"></td>
					<td>Couleur des bordures</td>
					<td>
						<input type="color" name="couleur_listing_bordures" class="colorpicker" value="<?php echo $couleur_listing_bordures; ?>" />
					</td>
				</tr>
			</table>
			<h4>Mode d'affichage</h4>
			<p>Affichez vos annonces selon vos envies en fonction de votre stock.</p>
			<p><strong>Affichage du listing :</strong></p>
			<table border="0">
				<tr>
					<td><img src="<?php echo plugin_dir_url(__FILE__); ?>images/galerie.jpg" alt="Galerie" /></td>
					<td><input type="radio" name="site_mode_listing" value="galerie" id="galerie"<?php if($site_mode_listing=='galerie'){ echo ' checked="checked"'; } ?> /> <label for="galerie">Mode galerie</label></td>
					<td width="20"></td>
					<td><img src="<?php echo plugin_dir_url(__FILE__); ?>images/galeriebig.jpg" alt="Galerie big" /></td>
					<td><input type="radio" name="site_mode_listing" value="galeriebig" id="galeriebig"<?php if($site_mode_listing=='galeriebig'){ echo ' checked="checked"'; } ?> /> <label for="galeriebig">Mode galerie grand format</label></td>
				</tr>
				<tr>
					<td><img src="<?php echo plugin_dir_url(__FILE__); ?>images/liste.jpg" alt="Liste" /></td>
					<td><input type="radio" name="site_mode_listing" value="liste" id="liste"<?php if($site_mode_listing=='liste'){ echo ' checked="checked"'; } ?> /> <label for="liste">Mode liste</label></td>
					<td width="20"></td>
					<td><img src="<?php echo plugin_dir_url(__FILE__); ?>images/listebig.jpg" alt="Liste big" /></td>
					<td><input type="radio" name="site_mode_listing" value="listebig" id="listebig"<?php if($site_mode_listing=='listebig'){ echo ' checked="checked"'; } ?> /> <label for="listebig">Mode liste grand format</label></td>
				</tr>
			</table>
			<hr />
			<h3>Page de détail</h3>
			<p>Créez votre page de détail en y insérant dans son contenu le tag ci-dessous.</p>
			<code>[annoncesautomobile_detail]</code>
			<p>Indiquez la page sur laquelle figure votre tag de détail.</p>
			<p>Page de détail : <select name="detail"><?php
				$pages = get_pages();
				foreach($pages as $page)
				{
					$selected = '';
					if($pagedetail==$page->ID){ $selected = ' selected="selected"'; }
					echo '<option value="'.$page->ID.'"'.$selected.'>'.$page->post_title.'</option>';
				}
			?></select></p>
			<h4>Gestion des couleurs</h4>
			<table border="0">
				<tr>
					<td>Première couleur de fond des caractéristiques</td>
					<td>
						<input type="color" name="couleur_detail_liste1" class="colorpicker" value="<?php echo $couleur_detail_liste1; ?>" />
					</td>
					<td width="10"></td>
					<td>Seconde couleur de fond des caractéristiques</td>
					<td>
						<input type="color" name="couleur_detail_liste2" class="colorpicker" value="<?php echo $couleur_detail_liste2; ?>" />
					</td>
				</tr>
				<tr>
					<td>Couleur du prix</td>
					<td>
						<input type="color" name="couleur_detail_prix" class="colorpicker" value="<?php echo $couleur_detail_prix; ?>" />
					</td>
					<td width="10"></td>
					<td></td>
					<td></td>
				</tr>
			</table>
			<h4>Diaporama</h4>
			<p>Affichage des vignettes : <select name="slider_thumb">
							<option value="y"<?php if($slider_thumb=='y'){ echo ' selected="selected"'; } ?>>Oui</option>
							<option value="n"<?php if($slider_thumb=='n'){ echo ' selected="selected"'; } ?>>Non</option>
						</select>
			</p>
			<hr />
			<h3>Page de dépôt d'annonce</h3>
			<p>Créez votre page de dépôt d'annonce en y insérant dans son contenu le tag ci-dessous.</p>
			<code>[annoncesautomobile_depot]</code>
			<p>Indiquez la page sur laquelle figure votre tag de dépôt.</p>
			<p>Page de dépôt d'annonce : <select name="depot"><?php
				$pages = get_pages();
				foreach($pages as $page)
				{
					$selected = '';
					if($pagedepot==$page->ID){ $selected = ' selected="selected"'; }
					echo '<option value="'.$page->ID.'"'.$selected.'>'.$page->post_title.'</option>';
				}
			?></select></p>
			<hr />
			<p><input type="submit" value="Enregistrer" /></p>
		</form>
	</div>
<?php
}

function annoncesautomobile_admin_js()
{
	wp_enqueue_script('custom-js', plugins_url('annonces-automobile/admin.js' , dirname(__FILE__)));
}
function annoncesautomobile_admin_css() {
        wp_register_style('custom_wp_admin_css', plugins_url('annonces-automobile/admin.css' , dirname(__FILE__)), false, '1.0.0');
        wp_enqueue_style('custom_wp_admin_css');
}
add_action('admin_enqueue_scripts', 'annoncesautomobile_admin_css');
add_action('admin_enqueue_scripts', 'annoncesautomobile_admin_js');

function annoncesautomobile_depot()
{
	$html = '';
	
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	$annoncesautomobileurl = annoncesautomobile_wordpress_uploads_directory_url().'/annoncesautomobile/';
	
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	if(!is_dir($annoncesautomobilepath.'photos'))
	{
		mkdir($annoncesautomobilepath.'photos', 0777, true);
		mkdir($annoncesautomobilepath.'photos/big', 0777, true);
		mkdir($annoncesautomobilepath.'photos/small', 0777, true);
	}
	
	$pagedepot = '';
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$tabchoice = explode('|', $annonceschoice);
	if(isset($tabchoice[4])){ $pagedepot = $tabchoice[5]; }
	
	$etat = sanitize_text_field($_POST['etat']);
	$marque = sanitize_text_field($_POST['marque']);
	$modele = sanitize_text_field($_POST['modele']);
	$finition = sanitize_text_field($_POST['finition']);
	$categorie = sanitize_text_field($_POST['categorie']);
	$energie = sanitize_text_field($_POST['energie']);
	$mois = sanitize_text_field($_POST['mois']);
	$annee = sanitize_text_field($_POST['annee']);
	$km = sanitize_text_field($_POST['km']);
	$transmission = sanitize_text_field($_POST['transmission']);
	$nbportes = sanitize_text_field($_POST['nbportes']);
	$puissance_ch = sanitize_text_field($_POST['puissance_ch']);
	$puissance_cv = sanitize_text_field($_POST['puissance_cv']);
	$emission_co2 = sanitize_text_field($_POST['emission_co2']);
	$couleur_ext = sanitize_text_field($_POST['couleur_ext']);
	$couleur_int = sanitize_text_field($_POST['couleur_int']);
	$garantie_mois = sanitize_text_field($_POST['garantie_mois']);
	$garantie_constructeur = sanitize_text_field($_POST['garantie_constructeur']);
	$descriptif = sanitize_text_field($_POST['descriptif']);
	$prix = sanitize_text_field($_POST['prix']);
	$contact_temp = sanitize_text_field($_POST['contact_temp']);
	$adresse_temp = sanitize_text_field($_POST['adresse_temp']);
	$departement_temp = sanitize_text_field($_POST['departement_temp']);
	$commune_temp = sanitize_text_field($_POST['commune_temp']);
	$cp_temp = sanitize_text_field($_POST['cp_temp']);
	$tel1_temp = sanitize_text_field($_POST['tel1_temp']);
	$tel2_temp = sanitize_text_field($_POST['tel2_temp']);
	$email1_temp = sanitize_text_field($_POST['email1_temp']);
	$email2_temp = sanitize_text_field($_POST['email2_temp']);
	
	$tmarque = array();
	$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/marques.php?key=aA$Xm1&all=true');
	foreach($xml->marque as $m){ $tmarque[] = array($m->id, $m->nom); }
	
	$tmodele = array();
	if(isset($marque) && !empty($marque))
	{
		$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/series.php?key=aA$Xm1&marque='.$marque.'&all=true');
		foreach($xml->serie as $m){ $tmodele[] = array($m->id, $m->nom); }
	}
	
	$tenergie = array();
	$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/energies.php?key=aA$Xm1');
	foreach($xml->energie as $e){ $tenergie[] = array($e->id, $e->nom); }
	
	$tcategorie = array();
	$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/categories.php?key=aA$Xm1');
	foreach($xml->categorie as $c){ $tcategorie[] = array($c->id, $c->nom); }
	
	$tdepartement = array();
	$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/departements.php?key=aA$Xm1');
	foreach($xml->departement as $d){ $tdepartement[] = array($d->id, $d->num.' - '.$d->nom); }
	
	$tcommune = array();
	if(isset($departement_temp) && !empty($departement_temp))
	{
		$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/communes.php?key=aA$Xm1&departement='.$departement_temp);
		foreach($xml->commune as $c){ $tcommune[] = array($c->id, $c->nom); }
	}
	
	$currentyear = date("Y");
	$tannee = array();
	for($i=$currentyear;$i>=($currentyear-60);$i--){ $tannee[] = $i; }
	
	$tmois = array();
	for($i=1;$i<=12;$i++)
	{
		$zero = "";
		if(strlen($i)==1){ $zero = "0"; }
		$tmois[] = $zero.$i;
	}
	
	$tnbmois = array();
	for($i=1;$i<=36;$i++){ $tnbmois[] = $i; }
	
	$_SESSION['id_client'] = md5($_SERVER['REMOTE_ADDR']);
	
	$returnpaiement = false;
	$reponse = '';
	
	if(isset($_POST['action'])){ $postaction = sanitize_text_field($_POST['action']); }
	if(isset($postaction) && $postaction=='depot-annonce')
	{
		$sendemail = true;
		$reponsemail = '';
		
		$postetat = '';
		$postmarque = '';
		$postmodele = '';
		$postfinition = '';
		$postcategorie = '';
		$postenergie = '';
		$postmois = '';
		$postannee = '';
		$postkm = '';
		$posttransmission = '';
		$postnbportes = '';
		$postpuissance_cv = '';
		$postpuissance_ch = '';
		$postemission_co2 = '';
		$postcouleur_ext = '';
		$postcouleur_int = '';
		$postgarantie_mois = '';
		$postgarantie_constructeur = '';
		$postdescriptif = '';
		$postprix = '';
		$postcontact_temp = '';
		$postadresse_temp = '';
		$postdepartement_temp = '';
		$postcommune_temp = '';
		$postcp_temp = '';
		$posttel1_temp = '';
		$posttel2_temp = '';
		$postemail1_temp = '';
		$postemail2_temp = '';
		
		if(isset($_POST['etat'])){ $postetat = sanitize_text_field($_POST['etat']); }
		if(isset($_POST['marque'])){ $postmarque = sanitize_text_field($_POST['marque']); }
		if(isset($_POST['modele'])){ $postmodele = sanitize_text_field($_POST['modele']); }
		if(isset($_POST['finition'])){ $postfinition = sanitize_text_field($_POST['finition']); }
		if(isset($_POST['categorie'])){ $postcategorie = sanitize_text_field($_POST['categorie']); }
		if(isset($_POST['energie'])){ $postenergie = sanitize_text_field($_POST['energie']); }
		if(isset($_POST['mois'])){ $postmois = sanitize_text_field($_POST['mois']); }
		if(isset($_POST['annee'])){ $postannee = sanitize_text_field($_POST['annee']); }
		if(isset($_POST['km'])){ $postkm = sanitize_text_field($_POST['km']); }
		if(isset($_POST['transmission'])){ $posttransmission = sanitize_text_field($_POST['transmission']); }
		if(isset($_POST['nbportes'])){ $postnbportes = sanitize_text_field($_POST['nbportes']); }
		if(isset($_POST['puissance_cv'])){ $postpuissance_cv = sanitize_text_field($_POST['puissance_cv']); }
		if(isset($_POST['puissance_ch'])){ $postpuissance_ch = sanitize_text_field($_POST['puissance_ch']); }
		if(isset($_POST['emission_co2'])){ $postemission_co2 = sanitize_text_field($_POST['emission_co2']); }
		if(isset($_POST['couleur_ext'])){ $postcouleur_ext = sanitize_text_field($_POST['couleur_ext']); }
		if(isset($_POST['couleur_int'])){ $postcouleur_int = sanitize_text_field($_POST['couleur_int']); }
		if(isset($_POST['garantie_mois'])){ $postgarantie_mois = sanitize_text_field($_POST['garantie_mois']); }
		if(isset($_POST['garantie_constructeur'])){ $postgarantie_constructeur = sanitize_text_field($_POST['garantie_constructeur']); }
		if(isset($_POST['descriptif'])){ $postdescriptif = sanitize_text_field($_POST['descriptif']); }
		if(isset($_POST['prix'])){ $postprix = sanitize_text_field($_POST['prix']); }
		if(isset($_POST['contact_temp'])){ $postcontact_temp = sanitize_text_field($_POST['contact_temp']); }
		if(isset($_POST['adresse_temp'])){ $postadresse_temp = sanitize_text_field($_POST['adresse_temp']); }
		if(isset($_POST['departement_temp'])){ $postdepartement_temp = sanitize_text_field($_POST['departement_temp']); }
		if(isset($_POST['commune_temp'])){ $postcommune_temp = sanitize_text_field($_POST['commune_temp']); }
		if(isset($_POST['cp_temp'])){ $postcp_temp = sanitize_text_field($_POST['cp_temp']); }
		if(isset($_POST['tel1_temp'])){ $posttel1_temp = sanitize_text_field($_POST['tel1_temp']); }
		if(isset($_POST['tel2_temp'])){ $posttel2_temp = sanitize_text_field($_POST['tel2_temp']); }
		if(isset($_POST['email1_temp'])){ $postemail1_temp = sanitize_text_field($_POST['email1_temp']); }
		if(isset($_POST['email2_temp'])){ $postemail2_temp = sanitize_text_field($_POST['email2_temp']); }
		
		$photosmall = array();
		$photobig = array();
		for($i=1;$i<=11;$i++)
		{
			if(file_exists($annoncesautomobilepath.'photos/big/'.$_SESSION["id_client"].'/'.$i.'.jpg')){ $photobig[] = $annoncesautomobileurl.'photos/big/'.$_SESSION["id_client"].'/'.$i.'.jpg'; }
		}
		
		$postphotobig = implode(';',$photobig);
		
		$args = array(
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
			),
			'body' => "key=aA$!n5ert&etat=".$postetat."&marque=".$postmarque."&modele=".$postmodele."&finition=".$postfinition."&categorie=".$postcategorie."&energie=".$postenergie."&mois=".$postmois."&annee=".$postannee."&km=".$postkm."&transmission=".$posttransmission."&nbportes=".$postnbportes."&puissance_cv=".$postpuissance_cv."&puissance_ch=".$postpuissance_ch."&emission_co2=".$postemission_co2."&couleur_ext=".$postcouleur_ext."&couleur_int=".$postcouleur_int."&garantie_mois=".$postgarantie_mois."&garantie_constructeur=".$postgarantie_constructeur."&descriptif=".$postdescriptif."&prix=".$postprix."&contact=".$postcontact_temp."&adresse=".$postadresse_temp."&departement=".$postdepartement_temp."&commune=".$postcommune_temp."&cp=".$postcp_temp."&tel1=".$posttel1_temp."&tel2=".$posttel2_temp."&email1=".$postemail1_temp."&email2=".$postemail2_temp."&photobig=".$postphotobig."&origine=".$_SERVER['HTTP_HOST']
		);
		$server_output = wp_remote_post('https://www.annonces-automobile.com/script/xml/depot_annonce.php', $args);
		$reponse = $server_output['body'];
		if(is_numeric($reponse))
		{
			$reponsemail = $reponse;
			$returnpaiement = true;
		}
		else
		{
			$reponsemail = $reponse;
		}
	}
	
	if($returnpaiement)
	{
		$html = '<h2>Validez votre annonce</h2>
				<p>Votre annonce a bien été enregistrée !</p>
				<p>Validez la mise en ligne de votre annonce en procédant au paiement de 29 &euro; grâce au service Stripe.</p>
				<iframe width="100%" height="600" src="https://www.annonces-automobile.com/script/xml/valider_transaction.php?transaction='.$reponsemail.'">
				</iframe>';
	}
	else
	{
		$html = '<p><strong>Déposez votre annonce sur '.$_SERVER['SERVER_NAME'].' et Annonces-Automobile.com pour 29 &euro; !</strong></p>
				<p>Ci-dessous, décrivez votre annonce et ajoutez des photos. Nous vous recommandons d\'être précis dans votre description et d\'ajouter des photos de bonne qualité.</p>
				<div id="depot-annonce">';
			$html .= '<h2>Photos du véhicule</h2>
					<p>Commencez par ajouter les photos de votre véhicule. Attention, les photos doivent être au format JPG avec un poid maximal de 4 Mo.</p>
					<h3>Photo principale</h3>
					<table border="0">
						<tr>
							<td id="imgwrapper-photoannonce1">';
									$urlphoto = plugin_dir_url(__FILE__).'images/photo.jpg';
									$hidden = ' hidden';
									
									if(file_exists($annoncesautomobilepath.'photos/small/'.$_SESSION["id_client"].'/1.jpg'))
									{
										$urlphoto = $annoncesautomobileurl.'photos/small/'.$_SESSION["id_client"].'/1.jpg';
										$hidden = '';
									}
								$html .= '<img src="'.plugin_dir_url(__FILE__).'images/delete-img.png" alt="Supprimer la photo" class="deleteimgannonce '.$hidden.'" id="deleteimg-photoannonce1" for="photoannonce1" title="Supprimer la photo" />
								<img src="'.$urlphoto.'" width="100" class="preview" alt="Logo" id="photo-photoannonce1" />
							</td>
							<td>
								<form action="'.plugin_dir_url(__FILE__).'image-upload.php" onSubmit="return false" method="post" enctype="multipart/form-data" class="uploadform" id="uploadform-photoannonce1" for="photoannonce1">
									<input id="uploadannonce-photoannonce1" type="hidden" name="annonce" />
									<input id="uploadname-photoannonce1" type="hidden" name="name" value="1" />
									<input id="uploadurl-photoannonce1" type="hidden" name="url" value="'.$annoncesautomobileurl.'photos/small/'.$_SESSION["id_client"].'/" />
									<input id="uploadnone-photoannonce1" type="hidden" name="none" value="'.plugin_dir_url(__FILE__).'images/photo.jpg" />
									<input id="uploadtype-photoannonce1" type="hidden" name="type" value="jpg" />
									<input name="image" class="imageinput hidden" for="photoannonce1" id="imageinput-photoannonce1" type="file" /><label for="imageinput-photoannonce1" class="button-black">Importer</label>
									<div id="loadingimg-photoannonce1" class="hidden loadingimg"><img src="'.plugin_dir_url(__FILE__).'images/loader.gif" alt="Chargement..." /></div>
									<div id="output-photoannonce1" class="output error"></div>
									<input type="hidden" name="id_client" value="'.$_SESSION['id_client'].'" />
								</form>
							</td>
						</tr>
					</table>
					<h3>Autres photos</h3>';
					$j = 0;
					for($i=2;$i<=11;$i++)
					{
						$j++;
						$html .= '<div class="twice">
									<table border="0">
										<tr>
											<td id="imgwrapper-photoannonce'.$i.'">';
													$urlphoto = plugin_dir_url(__FILE__).'images/photo.jpg';
													$hidden = ' hidden';
													
													if(file_exists($annoncesautomobilepath.'photos/small/'.$_SESSION["id_client"].'/'.$i.'.jpg'))
													{
														$urlphoto = $annoncesautomobileurl.'photos/small/'.$_SESSION["id_client"].'/'.$i.'.jpg';
														$hidden = '';
													}
												$html .= '<img src="'.plugin_dir_url(__FILE__).'images/delete-img.png" alt="Supprimer la photo" class="deleteimgannonce '.$hidden.'" id="deleteimg-photoannonce'.$i.'" for="photoannonce'.$i.'" title="Supprimer la photo" />
												<img src="'.$urlphoto.'" width="100" class="preview" alt="Logo" id="photo-photoannonce'.$i.'" />
											</td>
											<td>
												<form action="'.plugin_dir_url(__FILE__).'image-upload.php" onSubmit="return false" method="post" enctype="multipart/form-data" class="uploadform" id="uploadform-photoannonce'.$i.'" for="photoannonce'.$i.'">
													<input id="uploadannonce-photoannonce'.$i.'" type="hidden" name="annonce" />
													<input id="uploadname-photoannonce'.$i.'" type="hidden" name="name" value="'.$i.'" />
													<input id="uploadurl-photoannonce'.$i.'" type="hidden" name="url" value="'.$annoncesautomobileurl.'photos/small/'.$_SESSION["id_client"].'/" />
													<input id="uploadnone-photoannonce'.$i.'" type="hidden" name="none" value="'.plugin_dir_url(__FILE__).'images/photo.jpg" />
													<input id="uploadtype-photoannonce'.$i.'" type="hidden" name="type" value="jpg" />
													<input name="image" class="imageinput hidden" for="photoannonce'.$i.'" id="imageinput-photoannonce'.$i.'" type="file" /><label for="imageinput-photoannonce'.$i.'" class="button-black">Importer</label>
													<div id="loadingimg-photoannonce'.$i.'" class="hidden loadingimg"><img src="'.plugin_dir_url(__FILE__).'images/loader.gif" alt="Chargement..." /></div>
													<div id="output-photoannonce'.$i.'" class="output error"></div>
													<input type="hidden" name="id_client" value="'.$_SESSION['id_client'].'" />
												</form>
											</td>
										</tr>
									</table>
								</div>';
						if($j==2){ $html .= '<div class="clear"></div>'; $j=0; }
					}
					$html .= '</table>';
					$html .= '<form method="post" action="'.get_permalink($pagedepot).'#button-inscription">
						<input type="hidden" name="action" value="depot-annonce" />
						<h2>Caractériques de votre véhicule</h2>
						<div class="twice">Type / Etat <span class="red">*</span></div>
						<div class="twice">
							<select name="etat" id="etat">
								<option value=""></option>
								<option value="Occasion"';
								if(!empty($postetat) && $postetat=='Occasion'){ $html .= ' selected="selected"'; }
								$html .= '>Occasion</option>
								<option value="Neuf"';
								if(!empty($postetat) && $postetat=='Neuf'){ $html .= ' selected="selected"'; }
								$html .= '>Neuf</option>
							</select>
						</div>
						<div class="clear"></div>
						<div class="twice">Marque <span class="red">*</span></div>
						<div class="twice">
							<select name="marque" id="marque" data-for="modele" class="selectmarque">
								<option value=""></option>';
								foreach($tmarque as $m)
								{
									$selected = '';
									if(!empty($postmarque) && $m[0]==$postmarque){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$m[0].'"'.$selected.'>'.$m[1].'</option>';
								}
					$html .= '</select>
						</div>
						<div class="clear"></div>
						<div class="twice">Modèle <span class="red">*</span></div>
						<div class="twice">
							<select name="modele" id="modele">';
							if(count($tmodele)>0)
							{
								foreach($tmodele as $m)
								{
									$selected = '';
									if(!empty($postmodele) && $m[0]==$postmodele){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$m[0].'"'.$selected.'>'.$m[1].'</option>';
								}
							}
					$html .= '</select>
						</div>
						<div class="clear"></div>
						<div class="twice">Finition</div>
						<div class="twice"><input type="text" name="finition" value="'.$finition.'" /></div>
						<div class="clear"></div>
						<div class="twice">Catégorie <span class="red">*</span></div>
						<div class="twice">
							<select name="categorie">
								<option value=""></option>';
								foreach($tcategorie as $c)
								{
									$selected = '';
									if(!empty($postcategorie) && $c[0]==$postcategorie){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$c[0].'"'.$selected.'>'.$c[1].'</option>';
								}
					$html .= '</select>
						</div>
						<div class="clear"></div>
						<div class="twice">Energie <span class="red">*</span></div>
						<div class="twice">
							<select name="energie">
								<option value=""></option>';
								foreach($tenergie as $e)
								{
									$selected = '';
									if(!empty($postenergie) && $e[0]==$postenergie){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$e[0].'"'.$selected.'>'.$e[1].'</option>';
								}
					$html .= '</select>
						</div>
						<div class="clear"></div>
						<div class="twice">Mise en circulation</div>
						<div class="twice">
							<select name="mois" class="small">
								<option value=""></option>';
								foreach($tmois as $m)
								{
									$selected = '';
									if(!empty($postmois) && $m==$postmois){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$m.'"'.$selected.'>'.$m.'</option>';
								}
					$html .= '</select>&nbsp;/&nbsp;<select name="annee" class="small">
								<option value=""></option>';
								foreach($tannee as $a)
								{
									$selected = '';
									if(!empty($postannee) && $a==$postannee){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$a.'"'.$selected.'>'.$a.'</option>';
								}
					$html .= '</select>
						</div>
						<div class="clear"></div>
						<div class="twice">Kilométrage <span class="red">*</span></div>
						<div class="twice"><input type="text" name="km" value="'.$km.'" /></div>
						<div class="clear"></div>
						<div class="twice">Transmission</div>
						<div class="twice">
							<select name="transmission">
								<option value=""></option>
								<option value="Mecanique"';
								if(!empty($posttransmission) && $posttransmission=='Mecanique'){ $html .= ' selected="selected"'; }
								$html .= '>Mecanique</option>
								<option value="Automatique"';
								if(!empty($posttransmission) && $posttransmission=='Automatique'){ $html .= ' selected="selected"'; }
								$html .= '>Automatique</option>
							</select>
						</div>
						<div class="clear"></div>
						<div class="twice">Nb de portes</div>
						<div class="twice"><input type="text" name="nbportes" value="'.$postnbportes.'" /></div>
						<div class="clear"></div>
						<div class="twice">CH DIN</div>
						<div class="twice"><input type="text" name="puissance_ch" value="'.$postpuissance_ch.'" /></div>
						<div class="clear"></div>
						<div class="twice">CV fiscaux</div>
						<div class="twice"><input type="text" name="puissance_cv" value="'.$postpuissance_cv.'" /></div>
						<div class="clear"></div>
						<div class="twice">Emission CO2 (g/km)</div>
						<div class="twice"><input type="text" name="emission_co2" value="'.$postemission_co2.'" /></div>
						<div class="clear"></div>
						<div class="twice">Couleur extérieure</div>
						<div class="twice"><input type="text" name="couleur_ext" value="'.$postcouleur_ext.'" /></div>
						<div class="clear"></div>
						<div class="twice">Couleur intérieure</div>
						<div class="twice"><input type="text" name="couleur_int" value="'.$postcouleur_int.'" /></div>
						<div class="clear"></div>
						<div class="twice">Garantie</div>
						<div class="twice">
							<select name="garantie_mois">
								<option value=""></option>';
								foreach($tnbmois as $m)
								{
									$selected = '';
									if(!empty($postgarantie_mois) && $m==$postgarantie_mois){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$m.'"'.$selected.'>'.$m.' mois</option>';
								}
					$html .= '</select><br />
							<input type="checkbox" name="garantie_constructeur" value="y" id="garantie_constructeur"';
							if(!empty($postgarantie_constructeur) && $postgarantie_constructeur=='y'){ $html .= ' checked="checked"'; }
					$html .= ' />&nbsp;<label for="garantie_constructeur">Garantie constructeur</label>
						</div>
						<div class="clear"></div>
						<h2>Description et équipements du véhicule</h2>
						<p>Précisez ici toutes les informations supplémentaires sur votre véhicule que vous n\'avez pas pu renseigner dans la partie "Caractériques de votre véhicule".</p>
						<textarea name="descriptif" rows="10" cols="70">'.$postdescriptif.'</textarea>
						<h2>Prix de vente</h2>
						<p>Déterminez le prix de votre véhicule qui sera visible par les internautes.</p>
						<div class="twice">Prix (&euro; TTC) <span class="red">*</span></div>
						<div class="twice"><input type="text" name="prix" value="'.$postprix.'" /></div>
						<div class="clear"></div>';
				$html .= '<h2>Information de compte <span class="red">et coordonnées</span></h2>
						<p>Si vous avez déjà un compte sur Annonces-Automobile.com, utilisez le formulaire de connexion. Sinon, remplissez le formulaire d\'inscription.</p>
						<p>Attention, les informations de contact (Téléphone et e-mail) que vous saisissez sont importantes ! Elles seront utilisées par les internautes qui seront intéressés par votre annonce de voiture.</p>
							<h3>Je m\'inscris</h3>
							<div class="twice">Nom et<br />prénom <span class="red">*</span></div>
							<div class="twice"><input type="text" name="contact_temp" value="'.$postcontact_temp.'" /></div>
							<div class="clear"></div>
							<div class="twice">Adresse</div>
							<div class="twice"><input type="text" name="adresse_temp" value="'.$postadresse_temp.'" /></div>
							<div class="clear"></div>
							<div class="twice">Département<br />ou pays <span class="red">*</span></div>
							<div class="twice">
								<select name="departement_temp" id="departement" class="selectdepartement" data-for="commune">
									<option value=""></option>';
									foreach($tdepartement as $d)
									{
										$selected = '';
										if(!empty($postdepartement_temp) && $d[0]==$postdepartement_temp){ $selected = ' selected="selected"'; }
										
										$html .= '<option value="'.$d[0].'"'.$selected.'>'.$d[1].'</option>';
									}
						$html .= '</select>
							</div>
							<div class="clear"></div>
							<div class="twice">Commune <span class="red">*</span></div>
							<div class="twice">
								<select name="commune_temp" id="commune">
									<option value=""></option>';
									if(count($tcommune)>0)
									{
										foreach($tcommune as $c)
										{
											$selected = '';
											if(!empty($postcommune_temp) && $c[0]==$postcommune_temp){ $selected = ' selected="selected"'; }
											
											$html .= '<option value="'.$c[0].'"'.$selected.'>'.$c[1].'</option>';
										}
									}
						$html .= '</select>
							</div>
							<div class="clear"></div>
							<div class="twice">Code postal</div>
							<div class="twice"><input type="text" name="cp_temp" id="cp" value="'.$postcp_temp.'" /></div>
							<div class="clear"></div>
							<div class="twice">N° de tél. 1 <span class="red">*</span></div>
							<div class="twice"><input type="text" name="tel1_temp" value="'.$posttel1_temp.'" /></div>
							<div class="clear"></div>
							<div class="twice">N° de tél. 2</div>
							<div class="twice"><input type="text" name="tel2_temp" value="'.$posttel2_temp.'" /></div>
							<div class="clear"></div>
							<div class="twice">E-mail <span class="red">*</span></div>
							<div class="twice"><input type="text" name="email1_temp" value="'.$postemail1_temp.'" /></div>
							<div class="clear"></div>
							<div class="twice">Resaisissez votre e-mail <span class="red">*</span></div>
							<div class="twice"><input type="text" name="email2_temp" value="'.$postemail2_temp.'" /></div>
							<div class="clear"></div>
							<p><span class="smaller">En vous inscrivant sur Annonces-Automobile.com vous acceptez nos <a href="https://www.annonces-automobile.com/conditions-generales-de-vente" target="_blank" rel="noopener">conditions d\'utilisation</a></span>.</p>
							<p>Si vous êtes déjà inscrit, publiez votre annonce depuis le site <a href="https://www.annonces-automobile.com" target="_blank" rel="noopener">https://www.annonces-automobile.com</a></p>
							<div class="clear"></div><br />';
					if(!empty($reponse)){ $html .= '<p class="errormail"><strong>'.$reponsemail.'</strong></p>'; }
					$html .= '<p><input class="button-red" type="submit" id="button-inscription" value="Valider mon annonce" /></p>
							<p class="right smaller"><span class="red">*</span> Données obligatoires pour valider votre annonce</p>
						</form>
					</div>';
	}
	
	return $html;
}

function annoncesautomobile_listing()
{
	$html = '';
	
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	
	$annonceschoice = 'all|';
	$affichage = '';
	$affichageparam = '';
	$pagelisting = '';
	$pagedetail = '';
	$nbparpage = 10;
	$nbresult = 0;
	$page = 1;
	$couleur_listing_bordures = '#eaeaea';
	$couleur_listing_fond = '#ffffff';
	$couleur_listing_fond_hover = '#FFEBEB';
	$couleur_listing_titre = '#d40000';
	$couleur_listing_texte = '#333333';
	$couleur_listing_prix = '#d40000';
	$site_mode_listing = 'liste';
	
	if(isset($_GET['plisting'])){ $getlisting = sanitize_text_field($_GET['plisting']); }
	if(isset($getlisting)){ $page = $getlisting; }
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$tabchoice = explode('|', $annonceschoice);
	if(isset($tabchoice[0])){ $affichage = $tabchoice[0]; }
	if(isset($tabchoice[1])){ $affichageparam = $tabchoice[1]; }
	if(isset($tabchoice[2])){ $pagelisting = $tabchoice[2]; }
	if(isset($tabchoice[3])){ $pagedetail = $tabchoice[3]; }
	if(isset($tabchoice[4])){ $nbparpage = $tabchoice[4]; }
	if(isset($tabchoice[6]))
	{
		$couleurs = explode(';', $tabchoice[6]);
		
		if(isset($couleurs[0]) && !empty($couleurs[0])){ $couleur_listing_bordures = $couleurs[0]; }
		if(isset($couleurs[1]) && !empty($couleurs[1])){ $couleur_listing_fond = $couleurs[1]; }
		if(isset($couleurs[2]) && !empty($couleurs[2])){ $couleur_listing_fond_hover = $couleurs[2]; }
		if(isset($couleurs[3]) && !empty($couleurs[3])){ $couleur_listing_titre = $couleurs[3]; }
		if(isset($couleurs[4]) && !empty($couleurs[4])){ $couleur_listing_texte = $couleurs[4]; }
		if(isset($couleurs[5]) && !empty($couleurs[5])){ $couleur_listing_prix = $couleurs[5]; }
	}
	if(isset($tabchoice[7])){ $site_mode_listing = $tabchoice[7]; }
	
	$xmlfile = 'https://www.annonces-automobile.com/script/xml/annonces.php?key=aA$Xm1';
	if(isset($_GET['marque'])){ $getmarque = sanitize_text_field($_GET['marque']); }
	if(isset($getmarque))
	{
		$xmlfile .= '&marque='.$getmarque;
	}
	if(isset($_GET['modele'])){ $getmodele = sanitize_text_field($_GET['modele']); }
	if(isset($getmodele))
	{
		$xmlfile .= '&serie='.$getmodele;
	}
	if(isset($_GET['energie'])){ $getmodele = sanitize_text_field($_GET['energie']); }
	if(isset($getmodele))
	{
		$xmlfile .= '&energie='.$getmodele;
	}
	if(isset($_GET['km'])){ $getkm = sanitize_text_field($_GET['km']); }
	if(isset($getkm))
	{
		$xmlfile .= '&km='.$getkm;
	}
	if(isset($_GET['prix_max'])){ $getprix_max = sanitize_text_field($_GET['prix_max']); }
	if(isset($getprix_max))
	{
		$xmlfile .= '&prix_max='.$getprix_max;
	}
	if(isset($_GET['prix_min'])){ $getprix_min = sanitize_text_field($_GET['prix_min']); }
	if(isset($getprix_min))
	{
		$xmlfile .= '&prix_min='.$getprix_min;
	}
	if(isset($_GET['categorie'])){ $getcategorie = sanitize_text_field($_GET['categorie']); }
	if(isset($getcategorie))
	{
		$xmlfile .= '&categorie='.$getcategorie;
	}
	if($affichage=='marque' && empty($getmarque))
	{
		$xmlfile .= '&marque='.$affichageparam;
	}
	else if($affichage=='categorie' && empty($getcategorie))
	{
		$xmlfile .= '&categorie='.$affichageparam;
	}
	else if($affichage=='client')
	{
		$xmlfile .= '&client='.$affichageparam;
	}
	
	$xmlcount = simplexml_load_file($xmlfile.'&mode=count');
	if(isset($xmlcount[0]) && !empty($xmlcount[0])){ $nbresult = $xmlcount[0]; }
	
	if($page==1)
	{
		$xmlfile .= '&limit_from='.$nbparpage;
	}
	else if($page>1)
	{
		$xmlfile .= '&limit_from='.(($page-1)*$nbparpage).'&limit='.$nbparpage;
	}
	$xml = simplexml_load_file($xmlfile);
	
	$i=0;
	$j=0;
	
	foreach($xml->annonce as $ann)
	{
		$i++;
		$j++;
		if($i==4){ $i=1; }
		if($j==3){ $j=1; }
		
		$html .= '<div class="annonce annonce'.$site_mode_listing.' triple'.$i.' paire'.$j.'" style="border:1px solid '.$couleur_listing_bordures.';background-color:'.$couleur_listing_fond.';">
					<div class="image">
						<img src="'.annoncesautomobile_image($ann->photo2).'" width="300" height="225" alt="'.$ann->marque.' '.$ann->modele.'" style="border:1px solid '.$couleur_listing_bordures.';" class="zoom" />
					</div>
					<div class="descriptif"';
					if($site_mode_listing=='galeriebig')
					{
						$rgb = hex2RGB($couleur_listing_fond, true);
						$html .= ' style="background:rgba('.$rgb.', 0.8);"';
					}
					$html .= '>
						<h3 class="titre" title="'.$ann->marque.' '.$ann->serie.' '.$ann->finition.'"><a href="'.get_permalink($pagedetail).'?annonce='.$ann->id.'" style="color:'.$couleur_listing_titre.';">'.$ann->marque.' '.$ann->serie.' '.$ann->finition.'</a></h3>
						<div class="separator"></div>
						<div class="caract" style="color:'.$couleur_listing_texte.';">';
						
						$date = explode('-', $data['mise_circulation']);
						$descriptif = '';
						$cat = trim($ann->categorie);
						if(!empty($cat)){ $descriptif .= $ann->categorie.' - '; }
						if(!empty($ann->annee)){ $descriptif .= $ann->annee; }
						if($site_mode_listing=='galeriebig'){ $descriptif .= ' - '; }else{ $descriptif .= '<br />'; }

						if(!empty($ann->energie) && $ann->energie!='N. C.'){ $descriptif .= $ann->energie.' - '; }
						if(!empty($ann->km) && $ann->km!=0){ $descriptif .= number_format(intval($ann->km), 0, ',', '.').' Km - '; }
						
						$descriptif = trim($descriptif);
						if(substr($descriptif, -1)=='-'){ $descriptif = substr($descriptif, 0, -1); }
						
			$html .= $descriptif;
			$html .= '</div>
					</div>
					<div class="prix" style="color:'.$couleur_listing_prix.';';
					if($site_mode_listing=='galeriebig')
					{
						$rgb = hex2RGB($couleur_listing_fond, true);
						$html .= 'background:rgba('.$rgb.', 0.8);';
					}
					$html .= '">'.number_format(intval($ann->prix), 0, ',', '.').' &euro;</div>
					<div class="clear"></div>
				</div>';
	}
	
	$annoncetrouvee = 'Aucune annonce trouvée pour votre recherche.';
	if($nbresult==1){ $annoncetrouvee = '1 annonce trouvée'; }
	if($nbresult>1){ $annoncetrouvee = $nbresult.' annonces trouvées'; }
	
	$html .= '<div class="clear"></div><p class="resultats_annonce">'.$annoncetrouvee.'</p>';
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$html .= annoncesautomobile_numero_page($nbresult, $nbparpage, $page, $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	
	return $html;
}

function annoncesautomobile_detail()
{
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$couleur_detail_prix = '#d40000';
	$couleur_detail_liste1 = '#FFFFFF';
	$couleur_detail_liste2 = '#EEEEEE';
	$slider_thumb = 'y';
	
	$tabchoice = explode('|', $annonceschoice);
	if(isset($tabchoice[0])){ $affichage = $tabchoice[0]; }
	if(isset($tabchoice[1])){ $affichageparam = $tabchoice[1]; }
	if(isset($tabchoice[2])){ $pagelisting = $tabchoice[2]; }
	if(isset($tabchoice[3])){ $pagedetail = $tabchoice[3]; }
	if(isset($tabchoice[4])){ $nbparpage = $tabchoice[4]; }
	if(isset($tabchoice[8]))
	{
		$couleurs = explode(';', $tabchoice[8]);
		
		if(isset($couleurs[0]) && !empty($couleurs[0])){ $couleur_detail_prix = $couleurs[0]; }
		if(isset($couleurs[1]) && !empty($couleurs[1])){ $couleur_detail_liste1 = $couleurs[1]; }
		if(isset($couleurs[2]) && !empty($couleurs[2])){ $couleur_detail_liste2 = $couleurs[2]; }
	}
	if(isset($tabchoice[9])){ $slider_thumb = $tabchoice[9]; }
	
	$tdepartement = array();
	$xml = simplexml_load_file('https://www.annonces-automobile.com/script/xml/departements.php?key=aA$Xm1');
	foreach($xml->departement as $d){ $tdepartement[] = array($d->id, $d->nom); }
	
	$html = '';
	$getannonce = '';
	
	$xmlfile = 'https://www.annonces-automobile.com/script/xml/annonce.php?key=aA$Xm1';
	if(isset($_GET['annonce'])){ $getannonce = sanitize_text_field($_GET['annonce']); }
	if(isset($getannonce) && !empty($getannonce)){ $xmlfile .= '&annonce='.$getannonce; }
	$xml = simplexml_load_file($xmlfile);
	
	$vehicule = htmlentities($xml->marque.' '.$xml->serie.' '.$xml->finition);
	
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	
	$returnemail = false;
	$sendemail = false;
	
	if(isset($_POST['action'])){ $postaction = sanitize_text_field($_POST['action']); }
	if(isset($postaction) && $postaction=='envoi-email')
	{
		$sendemail = true;
		$reponsemail = '';
		
		$postnom = '';
		$postemail = '';
		$posttel = '';
		$postdepartement = '';
		$postmessage = '';
		
		if(isset($_POST['nom'])){ $postnom = sanitize_text_field($_POST['nom']); }
		if(isset($_POST['email'])){ $postemail = sanitize_text_field($_POST['email']); }
		if(isset($_POST['tel'])){ $posttel = sanitize_text_field($_POST['tel']); }
		if(isset($_POST['departement'])){ $postdepartement = sanitize_text_field($_POST['departement']); }
		if(isset($_POST['message'])){ $postmessage = sanitize_text_field($_POST['message']); }
		
		$args = array(
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
			),
			'body' => "key=aA$!n5ert&nom=".$postnom."&email=".$postemail."&tel=".$posttel."&departement=".$postdepartement."&message=".$postmessage."&annonce=".$getannonce."&origine=".$_SERVER['HTTP_HOST']
		);
		$server_output = wp_remote_post( 'https://www.annonces-automobile.com/script/xml/email_envoi.php', $args );
		$reponse = $server_output['body'];
		if($reponse!='0')
		{
			if($reponse=='1')
			{
				$reponsemail = 'Votre message a bien été envoyé';
				$returnemail = true;
			}
			else
			{
				$reponsemail = $reponse;
			}
		}
	}
	
	$html .= '<div id="vehicule" itemscope itemtype="http://schema.org/Product">
				<div id="titre">
					<h1 class="upper" itemprop="name">'.$vehicule.'</h1>
				</div>
				<div id="prix" class="box-red" style="color:'.$couleur_detail_prix.'">';
					if(intval($xml->prix)>100)
					{
						$html .= number_format(intval($xml->prix), 0, ',', '.').' &euro;';
					}
					else
					{
						echo 'Prix sur demande';
					}
		$html .= '</div>
					<div class="clear"></div>';
					
					$i=0;
					$bigimg = '';
					$smallimg = '';
			
					foreach($xml->photos2->photo as $photo)
					{
						$i++;
						$itemprop = '';
						if($i==1){ $itemprop = ' itemprop="image"'; }
						
						$bigimg .= '<li><a id="bigimg'.$i.'" href="'.str_replace('/intermediate/', '/big/', str_replace('.jpg', 'wm.jpg', $photo)).'" class="diapo" data-fancybox-group="gallery" title="'.$vehicule.' - '.$i.'"><img'.$itemprop.' width="720" height="540" class="bigimg" src="'.annoncesautomobile_image(str_replace('.jpg', 'wm.jpg', $photo)).'" alt="'.$vehicule.' - '.$i.'" /></a></li>';
					}
					if($slider_thumb=='y')
					{
						$i=0;
						foreach($xml->photos->photo as $photo)
						{
							$i++;
							$smallimg .= '<li><a data-slide-index="'.($i-1).'" href="#"><img class="smallimg" src="'.annoncesautomobile_image(str_replace('/annonce/normal/', '/annoncecli/snormal/', $photo)).'" alt="'.str_replace('"', '', $vehicule).' '.$couleur_ext.' '.$etat.' - '.$i.'" width="133" height="100" /></a></li>';
						}
					}
			
			if($i!=0)
			{
				$html .= '<div id="slider" class="box-light">
							<div id="bigimg">
								<ul id="bxslider">'.$bigimg.'</ul>
							</div>';
							if($slider_thumb=='y')
							{
								$html .= '<div id="smallimg">
											<ul>'.$smallimg.'</ul>
										</div>';
							}
					$html .= '<div class="clear"></div>
						</div>';
			}
			
		$html .= '<div class="clear"></div>
					<div id="contact-vendeur">';
						if($xml->client->contrat!='Particulier')
						{
							$html .= '<strong>'.$xml->client->nom.'</strong><br />
										'.$xml->client->adresse.'<br />'.$xml->client->cp.' '.ucfirst($xml->client->ville).'<br />Département : '.$xml->client->departement.' ('.$xml->client->departement_num.')';
						}
						else
						{
							$html .= '<strong>Particulier</strong><br />
										'.$xml->client->cp.' '.ucfirst($xml->client->ville).'<br />Département : '.$xml->client->departement.' ('.$xml->client->departement_num.')';
						}
		$html .= '</div>
					<div id="contact-bouton">
						<a href="#" id="contact-tel-btn" class="button-green button-phone">Contacter par téléphone</a>';
						if(!$sendemail){ $html .= '<a href="#contact" id="contact-email-btn" class="button-green button-mail">Contacter par e-mail</a>'; }
			$html .= '</div>
					<div class="clear"></div>
					<div id="contact"';
					if(!$sendemail){ $html .= ' class="hidden"'; }
			$html .= '><h3>Contacter le vendeur par e-mail</h3>';
			if(!$returnemail)
			{
				if(!empty($reponsemail)){ $html .= '<p class="errormail"><strong>'.$reponsemail.'</strong></p>'; }
				$html .= '<form method="post" action="'.$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#contact-vendeur" id="form-envoi-email">
							<input type="hidden" name="client" id="client" value="'.$xml->client->id.'" />
							<input type="hidden" name="action" value="envoi-email" />
							<strong>Nom &amp; prénom</strong><br />
							<input type="text" name="nom" value="';
							if(isset($postnom) && !empty($postnom)){ $html .= $postnom; }
							$html .= '" placeholder="Nom &amp; prénom" /><br />
							<strong>N° de téléphone</strong><br />
							<input type="text" name="tel" value="';
							if(isset($posttel) && !empty($posttel)){ $html .= $posttel; }
							$html .= '" placeholder="N° de téléphone" /><br />
							<strong>Adresse e-mail</strong><br />
							<input type="text" name="email" value="';
							if(isset($postemail) && !empty($postemail)){ $html .= $postemail; }
							$html .= '" placeholder="Adresse e-mail" /><br />
							<strong>Département</strong><br />
							<select name="departement">
								<option value="" class="legend">Département</option>';
								foreach($tdepartement as $departement)
								{
									$selected = '';
									if(isset($postdepartement) && $departement[0]==$postdepartement){ $selected = ' selected="selected"'; }
									
									$html .= '<option value="'.$departement[0].'"'.$selected.'>'.$departement[1].'</option>';
								}
							$html .= '</select><br />
							<strong>Message</strong><br />
							<textarea name="message" cols="28" rows="8">';
							if(isset($postmessage) && !empty($postmessage)){ $html .= stripslashes($postmessage); }else{ $html .= "Bonjour,\nLa ".$vehicule." au prix de ".number_format(intval($xml->prix), 0, ',', '.')."&euro; sur du site Annonces Automobile m'intéresse.\nMerci de me contacter pour avoir plus d'information."; }
							$html .= '</textarea><br />
							<input type="submit" value="Envoyer le message" class="button-green button-mail" />
						</form>';
			}
			else
			{
				$html .= '<p class="successmail"><strong>'.$reponsemail.'</strong></p>';
			}
			$html .= '</div>
					<div class="clear"></div>
					<div itemprop="description">
						<div id="caracteristiques" class="box-light">
							<h2 class="box-titre">Caractéristiques du véhicule</h2>
							<table>
								<tr style="background-color:'.$couleur_detail_liste1.'">
									<td class="legend">Marque</td>
									<td><span itemprop="brand">'.$xml->marque.'</span></td>
									<td class="separator"></td>
									<td class="legend">Kilométrage</td>
									<td>';
									if(intval($xml->km)!=0){ $html .= $xml->km.' km'; }else{ $html .= '-'; }
						$html .= '</td>
								</tr>
								<tr style="background-color:'.$couleur_detail_liste2.'">
									<td class="legend">Modèle</td>
									<td>'.$xml->serie.'</td>
									<td class="separator"></td>
									<td class="legend">Mise en circulation</td>
									<td>';
									if($xml->annee!='00/0000'){ $html .= $xml->annee; }else{ $html .= '-'; }
						$html .= '</td>
								</tr>
								<tr style="background-color:'.$couleur_detail_liste1.'">
									<td class="legend">Finition</td>
									<td>';
									$finition = trim($xml->finition);
									if(!empty($finition)){ $html .= $finition; }else{ $html .= '-'; }
							$html .= '</td>
									<td class="separator"></td>
									<td class="legend">Garantie</td>
									<td>';
									$garantie = trim($xml->garantie);
									if(!empty($garantie)){ $html .= $garantie; }else{ $html .= '-'; }
						$html .= '</td>
								</tr>
								<tr style="background-color:'.$couleur_detail_liste2.'">
									<td class="legend">Catégorie</td>
									<td>'.$xml->categorie.'</td>
									<td class="separator"></td>
									<td class="legend">Couleur int.</td>
									<td>';
									$couleur_int = trim($xml->couleur_int);
									if(!empty($couleur_int)){ $html .= $couleur_int; }else{ $html .= '-'; }
						$html .= '</td>
								</tr>
								<tr style="background-color:'.$couleur_detail_liste1.'">
									<td class="legend">Energie</td>
									<td>';
									$energie = trim($xml->energie);
									if(!empty($energie)){ $html .= $energie; }else{ $html .= '-'; }
							$html .= '</td>
									<td class="separator"></td>
									<td class="legend">Couleur ext.</td>
									<td>';
									$couleur_ext = trim($xml->couleur_ext);
									if(!empty($couleur_ext)){ $html .= $couleur_ext; }else{ $html .= '-'; }
						$html .= '</td>
								</tr>
								<tr style="background-color:'.$couleur_detail_liste2.'">
									<td class="legend">Transmission</td>
									<td>';
									$transmission = trim($xml->transmission);
									if(!empty($transmission)){ $html .= $transmission; }else{ $html .= '-'; }
							$html .= '</td>
									<td class="separator"></td>
									<td class="legend">Nb. de portes</td>
									<td>';
									if(intval($xml->nb_portes)!=0){ $html .= $xml->nb_portes; }else{ $html .= '-'; }
							$html .= '</td>
								</tr>
								<tr style="background-color:'.$couleur_detail_liste1.'">
									<td class="legend">Puis. fiscale</td>
									<td>';
									if(intval($xml->puissance_fisc)!=0){ $html .= $xml->puissance_fisc.' cv'; }else{ $html .= '-'; }
							$html .= '</td>
									<td class="separator"></td>
									<td class="legend">Emission CO2</td>
									<td id="dpe-link">';
									$co2 = 0;
									if(intval($xml->co2)!=0){ $co2 = $xml->co2; }
									else if(intval($xml->puissance_fisc)!=0 && intval($xml->puissance_din)!=0 && intval($xml->puissance_fisc)<intval($xml->puissance_din))
									{
										$co2 = ceil((intval($xml->puissance_fisc) - pow(((intval($xml->puissance_din)*0.736)/40), 1.6)) * 45);
									}
									if($co2!=0){ $html .= $co2.' g/km'; }else{ $html .= '-'; }
						$html .= '</td>
								</tr>
								<tr style="background-color:'.$couleur_detail_liste2.'">
									<td class="legend">Puis. DIN</td>
									<td>';
									if(intval($xml->puissance_din)!=0){ $html .= $xml->puissance_din.' ch <span class="small">('.ceil($xml->puissance_din*0.736).' kW)</span>'; }else{ $html .= '-'; }
							$html .= '</td>
									<td class="separator"></td>
									<td class="legend">Référence</td>
									<td>';
									$reference = trim($xml->reference);
									if(!empty($reference)){ $html .= $reference; }else{ $html .= '-'; }
						$html .= '</td>
								</tr>
							</table>
						</div>
						<div class="clear"></div>';
						$descriptif = trim($xml->descriptif);
						if(!empty($descriptif))
						{
							$html .= '<div id="description" class="box-light">
										<h2 class="box-titre">Description et équipements</h2>
										<p>'.nl2br($descriptif).'</p>
										<div class="clear"></div></div>';
						}
				$html .= '</div>
					<div class="clear"></div>
				</div>';
	
	return $html;
}

function annoncesautomobile_listing_js()
{
	global $post;
	
	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$couleur_listing_fond = '#ffffff';
	$couleur_listing_fond_hover = '#FFEBEB';
	$couleur_listing_titre = '#d40000';
	$slider_thumb = 'y';
	
	$tabchoice = explode('|', $annonceschoice);
	if(isset($tabchoice[0])){ $affichage = $tabchoice[0]; }
	if(isset($tabchoice[1])){ $affichageparam = $tabchoice[1]; }
	if(isset($tabchoice[2])){ $pagelisting = $tabchoice[2]; }
	if(isset($tabchoice[3])){ $pagedetail = $tabchoice[3]; }
	if(isset($tabchoice[4])){ $nbparpage = $tabchoice[4]; }
	if(isset($tabchoice[5])){ $pagedepot = $tabchoice[5]; }
	if(isset($tabchoice[6]))
	{
		$couleurs = explode(';', $tabchoice[6]);
		
		if(isset($couleurs[1]) && !empty($couleurs[1])){ $couleur_listing_fond = $couleurs[1]; }
		if(isset($couleurs[2]) && !empty($couleurs[2])){ $couleur_listing_fond_hover = $couleurs[2]; }
		if(isset($couleurs[2]) && !empty($couleurs[2])){ $couleur_listing_titre = $couleurs[3]; }
	}
	if(isset($tabchoice[9])){ $slider_thumb = $tabchoice[9]; }
	
    echo '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bxslider/4.1.2/jquery.bxslider.min.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>
		<script>
			jQuery(function($){
				$(\'ul.paginationlisting a.current\').css(\'background-color\', \''.$couleur_listing_fond_hover.'\').css(\'color\', \''.$couleur_listing_titre.'\');
				$(\'.annonce,ul.paginationlisting a\').hover(function() {
					$(this).css(\'background-color\', \''.$couleur_listing_fond_hover.'\');
				}, function() {
					$(this).css(\'background-color\', \''.$couleur_listing_fond.'\');
				});
				
				$(\'.annonce\').click(function(){
					document.location = $(this).find(\'a\').attr(\'href\');
				});
			 
				$(\'.diapo\').fancybox({
					padding : 3
				});
				
				$(\'#slider\').show();
				$(\'#bxslider\').bxSlider({';
					if($slider_thumb=='y'){ echo 'pagerCustom:\'#smallimg\''; }
			echo '});
				
				$(\'#contact-email-btn\').click(function(){
					$(this).hide();
					$(\'#contact\').removeClass(\'hidden\');
					return false;
				});
				
				$(\'#contact-tel-btn\').click(function(){
					var contactbtn = $(this);
					$.ajax({
						dataType: "jsonp",
						url: "'.plugin_dir_url(__FILE__).'load_tel.php",
						data: {client:$(\'#client\').val()}
					}).success(function(msg){
						jQuery.each(msg, function(i, val){
							contactbtn.html(val);
						});
					});
					return false;
				});';
				if($post->ID==$pagedetail)
				{
					echo '$(\'.title,.entry-title\').each(function(){
						$(this).remove();
					});';
				}
			 echo '$(\'.selectmarque\').change(function(){
						var select = $(this);
						$(\'#\' + select.attr(\'data-for\'))
							.html(\'\')
							.append($(\'<option></option>\').val(\'\').html(\'Modèle\'));
						$.ajax({
							dataType: "jsonp",
							url: "'.plugin_dir_url(__FILE__).'load_modele.php",
							data: {currentmarque:select.val()';
							if($affichage=='client' && !empty($affichageparam) && $post->ID!=$pagedepot){ echo ',client:'.$affichageparam; }
							if($post->ID==$pagedepot){ echo ',all:true'; }
						echo '}
						}).success(function(msg){
							jQuery.each(msg, function(i, val){
								var modele = val.split(\'|\');
								$(\'#\' + select.attr(\'data-for\')).append($(\'<option></option>\').val(modele[0]).html(modele[1]));
							});
						});
					});
					$(\'.selectdepartement\').change(function(){
						var select = $(this);
						$(\'#\' + select.attr(\'data-for\'))
							.html(\'\')
							.append($(\'<option></option>\').val(\'\').html(\'Commune\'));
						$.ajax({
							dataType: "jsonp",
							url: "'.plugin_dir_url(__FILE__).'load_commune.php",
							data: {currentdepartement:select.val()}
						}).success(function(msg){
							jQuery.each(msg, function(i, val){
								var commune = val.split(\'|\');
								$(\'#\' + select.attr(\'data-for\')).append($(\'<option></option>\').val(commune[0]).html(commune[1]));
							});
						});
					});
					
					// FORMULAIRES IMAGES
					function beforeSubmit(f)
					{
						if(window.File && window.FileReader && window.FileList && window.Blob)
						{
							if(!$("#imageinput-" + f).val())
							{
								$("#output-" + f).html("Aucun fichier sélectionné !");
								return false
							}
							$("#loadingimg-" + f).show();
							$("#output-" + f).html("");  
						}
						else
						{
							$("#output-" + f).html("Votre navigateur Internet est trop ancien pour charger les photos !");
							return false;
						}
					}
					// Upload de l\'image
					$(".uploadform").submit(function(){
						var f = $(this).attr("for");
						var d = new Date();
						$(this).ajaxSubmit({
							target: "#output-" + f,
							beforeSubmit: beforeSubmit(f),
							success: function(msg){
								if(msg=="" || (msg!="" && !isNaN(msg)))
								{
									if(!isNaN(msg) && msg!="")
									{
										$("#uploadname-" + f).val(msg);
									}
									$("#photo-" + f).attr("src", $("#uploadurl-" + f).val() + $("#uploadname-" + f).val() + "." + $("#uploadtype-" + f).val() + "?v=" + d.getTime());
									$("#deleteimg-" + f).show();
								}
								$("#loadingimg-" + f).hide();
							},
							resetForm: true
						});
						return false;
					});
					$(".imageinput").change(function(){
						$("#uploadform-" + $(this).attr("for")).submit();
					});
					// Suppression de l\'image
					$(".deleteimgannonce").click(function(){
						if(confirm("Supprimer la photo ?"))
						{
							var f = $(this).attr("for");
							$.ajax({
								dataType: "jsonp",
								url: "'.plugin_dir_url(__FILE__).'supprimer-photo.php",
								data: {img:$("#uploadname-" + f).val(), id_client:"'.$_SESSION['id_client'].'"}
							}).success(function(){});
							
							$("#photo-" + f).attr("src", $("#uploadnone-" + f).val());
							$("#deleteimg-" +f).hide();
						}
					});
			 });
		</script>';
}

function annoncesautomobile_stripspecialchar($url)
{
    $url = utf8_decode($url);
    $url = strtr($url, utf8_decode('ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ()[]\'"~$&%*@ç!?;,:/\^¨€{}<>|+.- `³²�°´#×'), 'aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn    --      c  ---    e       ---32     x');
	$url = preg_replace('/([^.a-z0-9]+)/i', '-', $url);
    $url = str_replace(' ', '', $url);
    $url = str_replace('---', '-', $url);
    $url = str_replace('--', '-', $url);
    $url = trim($url, '-');
    return strtolower($url);
}

function annoncesautomobile_numero_page($nbresult, $per_page=15, $page=1, $url)
{
	global $nextpage;
	global $urlnextpage;
	global $urlprevpage;

	$annoncesautomobilepath = annoncesautomobile_wordpress_uploads_directory_path().'annoncesautomobile/';
	if(!is_dir($annoncesautomobilepath))
	{
		mkdir($annoncesautomobilepath, 0777, true);
		$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'w+');
		fclose($listingfile);
	}
	
	$couleur_listing_bordures = '#eaeaea';
	$couleur_listing_fond = '#ffffff';
	$couleur_listing_fond_hover = '#FFEBEB';
	$couleur_listing_texte = '#333333';
	
	$listingfile = fopen($annoncesautomobilepath.'listing.txt', 'r+');
	$listingline = fgets($listingfile);
	if(!empty($listingline)){ $annonceschoice = $listingline; }
	fclose($listingfile);
	
	$tabchoice = explode('|', $annonceschoice);
	if(isset($tabchoice[6]))
	{
		$couleurs = explode(';', $tabchoice[6]);
		
		if(isset($couleurs[0]) && !empty($couleurs[0])){ $couleur_listing_bordures = $couleurs[0]; }
		if(isset($couleurs[1]) && !empty($couleurs[1])){ $couleur_listing_fond = $couleurs[1]; }
		if(isset($couleurs[2]) && !empty($couleurs[2])){ $couleur_listing_fond_hover = $couleurs[2]; }
		if(isset($couleurs[4]) && !empty($couleurs[4])){ $couleur_listing_texte = $couleurs[4]; }
	}
	
	$style = ' style="color:'.$couleur_listing_texte.';background-color:'.$couleur_listing_fond.';border:1px solid '.$couleur_listing_bordures.';"';
	
	// Récupération et traitement de l'url courrante en supprimant la variable "plisting"
	$url = str_replace('&plisting=', '[*plisting=]', $url);
	$url = str_replace('?plisting=', '[!plisting=]', $url);
	$url = str_replace('&plisting', '&[plisting]', $url);
	$url = str_replace('?plisting', '?[plisting]', $url);
	$url = str_replace('[*plisting=]', '&plisting=', $url);
	$url = str_replace('[!plisting=]', '?plisting=', $url);
	$url = preg_replace('/&amp;plisting(=[^&]*)?|^plisting(=[^&]*)?&?/', '', $url);
	$url = preg_replace('/&plisting(=[^&]*)?|^plisting(=[^&]*)?&?/', '', $url);
	$url = preg_replace('/\?plisting(=[^&]*)?|^plisting(=[^&]*)?&?/', '', $url);
	$url = str_replace('&[plisting]', '&plisting', $url);
	$url = str_replace('?[plisting]', '?plisting', $url);
	$url = str_replace('&', '&amp;', $url);
	$separator = '&amp;';
	if(strrpos($url, '?') === false){ $separator = '?'; }
	$url = $url.$separator;
	
	$adjacents = "2";
	
	$prevlabel = "&lsaquo;";
	$nextlabel = "&rsaquo;";
	
	$page = ($page == 0 ? 1 : $page); 
	$start = ($page - 1) * $per_page;                              
	 
	$prev = $page - 1;                         
	$next = $page + 1;
	 
	$lastpage = ceil($nbresult/$per_page);
	 
	$lpm1 = $lastpage - 1; // //last page minus 1
	 
	$pagination = "";
	if($lastpage > 1){
		$pagination .= "<ul class=\"paginationlisting\">";
		$pagination .= "<li class=\"page_info\">Page {$page} sur {$lastpage}</li>";
			 
			if ($page > 1){
				$pagination.= "<li><a href=\"{$url}plisting={$prev}\"".$style.">{$prevlabel}</a></li>";
				$urlprevpage = "{$url}plisting={$prev}";
			}
			 
		if ($lastpage < 7 + ($adjacents * 2)){  
			for ($counter = 1; $counter <= $lastpage; $counter++){
				if ($counter == $page)
					$pagination.= "<li><a class=\"current\"".$style.">{$counter}</a></li>";
				else
					$pagination.= "<li><a href=\"{$url}plisting={$counter}\"".$style.">{$counter}</a></li>";                   
			}
		 
		} elseif($lastpage > 5 + ($adjacents * 2)){
			 
			if($page < 1 + ($adjacents * 2)) {
				 
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
					if ($counter == $page)
						$pagination.= "<li><a class=\"current\"".$style.">{$counter}</a></li>";
					else
						$pagination.= "<li><a href=\"{$url}plisting={$counter}\"".$style.">{$counter}</a></li>";                   
				}
				$pagination.= "<li><a href=\"{$url}plisting={$lastpage}\"".$style.">&rsaquo;&rsaquo;</a></li>";
					 
			} elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
				 
				$pagination.= "<li><a href=\"{$url}plisting=1\"".$style.">&lsaquo;&lsaquo;</a></li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
					if ($counter == $page)
						$pagination.= "<li><a class=\"current\"".$style.">{$counter}</a></li>";
					else
						$pagination.= "<li><a href=\"{$url}plisting={$counter}\"".$style.">{$counter}</a></li>";                   
				}
				$pagination.= "<li><a href=\"{$url}plisting={$lastpage}\"".$style.">&rsaquo;&rsaquo;</a></li>";     
				 
			} else {
				 
				$pagination.= "<li><a href=\"{$url}plisting=1\"".$style.">&lsaquo;&lsaquo;</a></li>";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $page)
						$pagination.= "<li><a class=\"current\"".$style.">{$counter}</a></li>";
					else
						$pagination.= "<li><a href=\"{$url}plisting={$counter}\"".$style.">{$counter}</a></li>";                   
				}
			}
		}
		 
			if($page<$counter-1)
			{
				$pagination.= "<li><a href=\"{$url}plisting={$next}\"".$style.">{$nextlabel}</a></li>";
				$urlnextpage = "{$url}plisting={$next}";
				
				if(isset($_GET['nbpp'])){ $getnbpp = sanitize_text_field($_GET['nbpp']); }
				if(isset($getnbpp) && $getnbpp=='all')
				{
					$nextpage = "<a class=\"nextlink\" href=\"{$url}plisting={$next}\"".$style.">{$nextlabel}</a>";
				}
			}
		 
		$pagination.= "</ul>";       
	}
	 
	return $pagination;
}

function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
    $rgbArray = array();
    if (strlen($hexStr) == 6) {
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) {
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false;
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
}