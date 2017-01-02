<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
//error_reporting(E_ALL);

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/composants.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/backoffice/cms/graphiques/charts.php');
activateMenu('gestioncontenu');

dirExists("/custom/graphiques/".$_SESSION['rep_travail']);

if($_GET['id']!="") $id = $_GET['id'];
if($_POST['id']!="") $id = $_POST['id'];

$composant = null;
if (strlen($id) > 0)
	$composant = getComposantById($id);


if($_GET['l']!="") $larg=$_GET['l'];
if($_POST['l']!="") $larg=$_POST['l'];

if($_GET['h']!="") $long=$_GET['h'];
if($_POST['h']!="") $long=$_POST['h'];

if($_GET['name']!="") $name=$_GET['name'];
if($_POST['name']!="") $name=$_POST['name'];

// patch serveur latecoere qui strip les \    -------------
if (is_post('antislashr')&&($_POST['antislashr']=='\r')){
	foreach($_POST as $kPost => $vPost){
		$_POST[$kPost] = str_replace('\r', '\\\r', $vPost);	
	}
}
// fin patch ----------------------------------------------

if(!(strlen($larg)>0 && strlen($long)>0)) {
//////////////////////////////////////////////////////////////
//////////////////     MODE INFO BRIQUE     //////////////////
////////////////// SAISIE DES INFOS DE BASE ////////////////// 
//////////////////////////////////////////////////////////////

	$_SESSION['chart_id']="";
	// premiere phase
	$name = '';
	$larg = '';
	$long = '';
	$type='';
	if ($composant != null) {
		$name = ' value="'.$composant['name'].'" ';
		$larg = ' value="'.$composant['width'].'" ';
		$long = ' value="'.$composant['height'].'" ';
		if($composant['type']!="Graphique") { echo "Ce script ne peut pas diter un type de brique autre que Graphique !"; return; }
		$type = ' value="'.$composant['type'].'" ';
		$contenuhtml = $composant['html'];
		$contenuhtml = preg_replace("/'/",'`',$contenuhtml);
	}
?>
<div class="ariane"><span class="arbo2">Créer une brique graphique</span></div>
<form name="creation" id="creation" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php if (strlen($id)>0) echo $id; ?>" />
<input type="hidden" name="chart_id" id="chart_id" value="<?php if (preg_match('/CHART_ID [0-9]+µ/msi', $contenuhtml)==1) echo preg_replace('/.*CHART_ID ([0-9]+)µ.*/msi', '$1', $contenuhtml); ?>" />

<input type="hidden" name="contenuhtml" id="contenuhtml" value='<?php if (strlen($contenuhtml)>0) echo $contenuhtml; ?>' />

<div class="arbo" id="briqueGraphicEditor">
    <div class="col_titre">
        Cr&eacute;ation de la brique Graphique
    </div>
        <div>
            <label for="name">Nom :</label>
            <input name="name" type="text" class="arbo" id="name" size="30"  maxlength="30" <?php echo $name; ?> />
        </div>
        <div>
            <label for="l">Largeur :</label>
            <input name="l" id="l" type="text" class="arbo" size="4" maxlength="4" <?php echo $larg; ?> /> <span>&nbsp;pixels</span>
        </div>
        <div>
            <label for="h">Hauteur :</label>
            <input name="h" id="h" type="text" class="arbo" size="4" maxlength="4" <?php echo $long; ?> /> <span>&nbsp;pixels</span>
        </div>
        <div>
            <input name="Suite (edition) >>" type="submit" class="arbo" id="SuiteButton" value="Suite (&eacute;dition) >>" >
        </div>
    </div>

<p class="warning"><b>Attention:</b> la taille d'une brique graphique ne peut pas d&eacute;passer 536px en largeur.</p>
</form>
<?php	
	if(!$_POST['init']==1 && !$_GET['init']==1){ // Reset des valeurs de sessions pour viter les conflits
		$_SESSION['HTMLcontent']='';
		$_SESSION['chart_data']='';
		$_SESSION['chart_type']='';
		$_SESSION['chart_title']='';
		$_SESSION['chart_legend']='';
		$_SESSION['chart_txt']='';
		$_SESSION['chart_hidedata']='';
	}

} else {
	// On dite ou visualise le bloc => enregistrement!
	// Enregistrement des modifs
	// Lecture des arguments passs par le formulaire
	// Si on a cliqu sur preview ou sur redimentionner => mmorisation des arguments
	if($_POST['xSize']>0) {
		$_POST['xSize'] = $_POST['xSize']+1;
		$_POST['ySize'] = $_POST['ySize']+1;
		$datas = array();
		$datas[0][0] = "";
		for($y=1;$y<$_POST['ySize'];$y++) {
			$datas[0][$y] = $_POST["libelle$y"];
		}
		for($x=1;$x<$_POST['xSize'];$x++) {
			$datas[$x][0] = $_POST["serie$x"];
			for($y=1;$y<$_POST['ySize'];$y++) {
				$datas[$x][$y] = $_POST["field$x$y"];
			}
		}
		

		$chart [ 'chart_data' ] = $datas;
		$chart [ 'chart_type' ] = "column"; // Valeur par dfaut
		if(strlen($_POST['chart_type'])>0) $chart [ 'chart_type' ] = $_POST['chart_type'];
		
		// Enregistrement des arguments mmoriss dans une session
		$_SESSION['chart_data'] = serialize($datas);
		$_SESSION['chart_type'] = $chart ['chart_type'];
		$_SESSION['chart_title'] = stripslashes($_POST['titre']);
		$_SESSION['chart_legend'] = stripslashes($_POST['legende']);
		$_SESSION['chart_txt'] = stripslashes($_POST['txtsupp']);
		$_SESSION['chart_hidedata'] = ($_POST['hidedata']!="")?'hide':'show';
	}
	else{
		//echo 'no data posted';
	}
	
	if(strlen($_POST['TYPEcontent']) > 0&&$_POST['preview']!="") {
	//////////////////////////////////////////////////////////////
	//////////////////       MODE PREVIEW       //////////////////
	////////////////// GENERATION DE LA BRIQUE  ////////////////// 
	//////////////////////////////////////////////////////////////
	
		$_SESSION['chart_id'] = $_POST['chart_id'];

		include_once('graphiques/charts_read_xml.php');		
		
		$chart['draw_text'][0]['width'] = $larg;
		$chart['draw_text'][0]['height'] = $long;
		$chart['draw_text'][0]['text'] = preg_replace("/[\r]?\n/msi",'\\\r',$_SESSION['chart_title']);
		
		$chart['draw_text'][1]['width'] = $larg;
		$chart['draw_text'][1]['height'] = $long;
		$chart['draw_text'][1]['text'] = preg_replace("/[\r]?\n/msi",'\\\r',$_SESSION['chart_legend']);
		
		$chart['draw_text'][2]['width'] = $larg;
		$chart['draw_text'][2]['height'] = $long;
		$chart['draw_text'][2]['text'] = preg_replace("/[\r]?\n/msi",'\\\r',$_SESSION['chart_txt']);
		
		
		// Paramtres par dfaut du flash
		//$chart [ 'canvas_bg' ] = array ( 'width' => $larg, 'height' => $long, 'color' => 'FFFFFF' );	
		$chart [ 'canvas_bg' ]['width'] = $larg;
		$chart [ 'canvas_bg' ]['height'] = $long;
		
		
		// Rduction de la zone du graphique en fonction du texte  ajouter	
		// Si titre => mesure de sa taille => enlve  la zone graphique
		$titre_h = 0;
		if ($_SESSION['chart_title']!="") {
			$titre_nb_lig = substr_count($_SESSION['chart_title'],"\n")+1;
			// Rcupre la taille du texte pour calculer la zone occup par le titre
			$titre_h = $chart[ 'draw_text' ][0]['size'] * $titre_nb_lig + 10 ;
		}
		
		$legend_h = 0;
		if ($_SESSION['chart_legend']!="") {
			$legend_nb_lig = substr_count($_SESSION['chart_legend'],"\n")+1;
			// Rcupre la taille du texte pour calculer la zone occup par le titre
			$legend_h = $chart[ 'draw_text' ][1]['size'] * $legend_nb_lig + 10 ;
		}
		
		$axes_legende_h=0;
		$legend_nb_lig = GetNbLig($datas);
		// Rcupre la taille du texte pour calculer la zone occup par les lgendes d'axe
		// lgende des abcisses du graph
		$axes_legende_h = $chart [ 'axis_value' ]['size'] * $legend_nb_lig + 70 ;
		
		$zonegraph_x=10;
		$zonegraph_y = $titre_h ;
		$zonegraph_w=$larg-20;
		$zonegraph_h = $long - $titre_h - $legend_h ;
		
		$legende_h=50; // hauteur inconnue de la lgende => constante
		
		$chart [ 'legend_rect' ]['y'] = $zonegraph_y+10;
		
		$txtsupp_w = 0;
		if ($_SESSION['chart_txt']!="") {
			// Rcupre la taille du texte pour calculer la zone occup par le titre
			$txtsupp_nb_car = strlen($_SESSION['chart_txt']);
			$txtsupp_w = 100 ;
			$nb_car_cesure = 15;
			if($txtsupp_nb_car>200) {
				// La zone de texte a besoin d'tre agrandie
				$augmentation_taille = floor($txtsupp_nb_car/33)+1;
				$txtsupp_w = $augmentation_taille * 13; // Augmentation de la zone texte supp
				$nb_car_cesure = floor($augmentation_taille * 2.3); // Augmentation de la limite pour csure
			}
			$chart [ 'legend_rect' ]['x'] = $zonegraph_x;
			$chart [ 'legend_rect' ]['width'] = $zonegraph_w-$txtsupp_w;
			$chart[ 'draw_text' ][0]['width'] = $larg-$txtsupp_w;
			$chart[ 'draw_text' ][1]['width'] = $larg-$txtsupp_w;
			$chart[ 'draw_text' ][2]['x'] = $larg-$txtsupp_w+5;
			$chart[ 'draw_text' ][2]['width'] = $txtsupp_w-30;
			$chart[ 'draw_text' ][2]['text'] = preg_replace("/[\r]?\n/msi",'\\\r',wordwrap( $chart[ 'draw_text' ][2]['text'] , $nb_car_cesure));
			$chart [ 'draw_rect' ] = array ( array (   'x'               =>  $larg-$txtsupp_w, 
											   'y'               =>  10, 
											   'width'           =>  $txtsupp_w-10,  
											   'height'          =>  $long-20, 
											   'rotation'        =>  0, 
											   'fill_color'      =>  "B6DE54", 
											   'fill_alpha'      =>  20, 
											   'line_color'      =>  "B6DE54", 
											   'line_alpha'      =>  100,
											   'line_thickness'  =>  1
										   )
								   ); 
		}
		
		
								   
		$chart['draw_line'][0]['x1'] =  $zonegraph_x;
		$chart['draw_line'][0]['y1'] =  $zonegraph_y;
		$chart['draw_line'][0]['x2'] =  $zonegraph_x+$zonegraph_w-$txtsupp_w;
		$chart['draw_line'][0]['y2'] =  $zonegraph_y;
		
		$chart['draw_line'][1]['x1'] =  $zonegraph_x;
		$chart['draw_line'][1]['y1'] =  $zonegraph_y+$zonegraph_h;
		$chart['draw_line'][1]['x2'] =  $zonegraph_x+$zonegraph_w-$txtsupp_w;
		$chart['draw_line'][1]['y2'] =  $zonegraph_y+$zonegraph_h;
		
		// Si camembert => la lgende coule partout
		// Rajustement pour l'affichage
		if(preg_match ( "/pie/msi", $chart['chart_type'] )) {
			$chart [ 'chart_grid_h' ]['alpha'] = 0; //  Bug pas de fond de graphique
			$chart[ 'legend_rect' ]['x'] = 10;
			$chart [ 'legend_rect' ]['width'] = 10;
			$chart [ 'legend_rect' ]['height'] = 10;
			$chart [ 'chart_value' ]['position']="outside";
			$axes_legende_h=40; // Il n'y a pas de lgendes sur camembert
			$legende_h=20; // Il n'y a pas de lgendes sur camembert
			$zonegraph_x+=floor($zonegraph_w/20);
			$zonegraph_w-=(floor($zonegraph_w/20)*2);
		}
		else { // Reduction du graphique
			$zonegraph_x+=40;
			$zonegraph_w-=80;
		}
		
		$chart[ 'chart_rect' ] = array ( 'x'=>$zonegraph_x, 'y'=>$zonegraph_y+$legende_h, 'width'=>$zonegraph_w-$txtsupp_w, 'height'=>($zonegraph_h-$axes_legende_h) );
		  
								   
		//	$chart[ 'legend_rect' ] = array ( 'x'=>-100, 'y'=>-100, 'width'=>10, 'height'=>10, 'margin'=>10 ); 
		
		// Nettoyage des donnes du tableau
		$chart [ 'chart_value_text' ] = $chart['chart_data'];
		for($i=1;$i<count($chart['chart_value_text']);$i++) { // Parcours toutes les lignes sauf la premiere
			// Supprime aussi la premire colonne
			$premcol = array_shift($chart['chart_data'][$i]);
			KeeponlyAllNumeric ($chart['chart_data'][$i]);
			array_unshift($chart['chart_data'][$i],$premcol);
		}
		
		// Si on ne veut pas afficher les donnes du graphique
		// On passe en argument un tableau avec des valeurs vides
		$chart['save_chart_value'] = $chart['chart_value_text'];
		if($_SESSION['chart_hidedata']=="hide") {
			// Remplit la premiere ligne de "null" => les titres ne sont pas touchs
			$chart['chart_value_text'][0] = array_fill(0,sizeof($chart['chart_value_text'][0]),null);
			for($i=1;$i<sizeof($chart['chart_value_text']);$i++) { // Parcours toutes les lignes sauf la premiere
				// Supprime aussi la premire colonne
				array_shift($chart['chart_value_text'][$i]);
				$chart['chart_value_text'][$i] = array_fill(0,sizeof($chart['chart_value_text'][$i]),"");
				array_unshift($chart['chart_value_text'][$i],null);
			}
		}
		
		
		// Mode cration => cration d'un ID spcial pour le graph
		if($id==""||$_SESSION['chart_id']=="") { 
			$id_graph = mt_rand(); 
			while( file_exists( $_SERVER['DOCUMENT_ROOT']."/custom/graphiques/".$_SESSION['rep_travail']."/graphic_".$id_graph.".php") ) {
				$id_graph = mt_rand();
			}
			$_SESSION['chart_id']=$id_graph;
		}
		
		// Ecriture des donnes du graphique en XML dans un fichier php
		// dans /modules/graphiques/xmldata
		ecrit_chart_data( $_SESSION['chart_id'], $chart );
		
		$htmlContent = "<!--CHART_ID ".$_SESSION['chart_id']."µ-->"."\n";
		$htmlContent .= "<!--CHART_TYPE ".$chart['chart_type']."µ-->"."\n";
		$htmlContent .= "<!--CHART_DATA ".urlencode(serialize($chart['save_chart_value']))."µ-->"."\n";
		$htmlContent .= "<!--CHART_TITLE ".urlencode($_SESSION['chart_title'])."µ-->"."\n";
		$htmlContent .= "<!--CHART_LEGEND ".urlencode($_SESSION['chart_legend'])."µ-->"."\n";
		$htmlContent .= "<!--CHART_TXT ".urlencode($_SESSION['chart_txt'])."µ-->"."\n";
		$htmlContent .= "<!--CHART_HIDEDATA ".$_SESSION['chart_hidedata']."µ-->"."\n";
		
		// api key
		$aKey = dbGetObjectsFromFieldValue('cms_chartskey', array('get_host'), array($_SERVER['HTTP_HOST']), NULL);
		if ((count($aKey) == 1)&&($aKey!=false)){
			$license = $aKey[0]->get_key();
		}
		
		// Gnration du code HTML pour le flash
		// Utilisation du script charts.php
		$htmlContent .= InsertChart ( "/backoffice/cms/graphiques/charts.swf", "/backoffice/cms/graphiques/charts_arg.php?id=".$_SESSION['chart_id'], $larg, $long , "ffffff", $transparent=false, $license);
		
		$htmlContentForm = preg_replace("/'/",'`',$htmlContent);

?>
<span class="arbo2"><strong>Aper&ccedil;u du composant Graphique <?php echo $name; ?>:</strong></span>
<form name="previewHTML" id="previewHTML" action="saveComposant.php?id=<?php if (strlen($id)>0) echo $id; ?>" method="post">
<input type="hidden" name="TYPEcontent" value="<?php echo $_POST['TYPEcontent'];?>">
<input type="hidden" name="WIDTHcontent" value="<?php echo $larg; ?>">
<input type="hidden" name="HEIGHTcontent" value="<?php echo $long; ?>">
<input type="hidden" name="NAMEcontent" value="<?php echo $name; ?>">
<input type="hidden" name="HTMLcontent" value='<?php echo $htmlContentForm; ?>'>
<hr width="<?php echo $larg; ?>" align="left" class="arbo2">
<!--div style="width: <?php echo $larg; ?>px; height: <?php echo $long; ?>px; background-color: #ffffff; overflow: auto;"-->
<?php echo $htmlContent; ?><br />
<!--/div-->
<hr width="<?php echo $larg; ?>" align="left" class="arbo2">
<span class="arbo"><u>Dimensions&nbsp;:</u>&nbsp;<?php echo $larg; ?> x <?php echo $long; ?>&nbsp;pixels</span>
<br />
<br />
<div>
<input name="<< Retour (modifier)" type="button" class="arbo" onClick="javascript:window.location='<?php echo $_SERVER['PHP_SELF']."?l=".$larg."&h=".$long."&init=1&name=".$name ;?>&id=<?php if (strlen($id)>0) echo $id; ?><?php if (isset($_GET['menuOpen'])) echo "&menuOpen=".$_GET['menuOpen']; ?>';" value="<< Retour (modifier)">
&nbsp;&nbsp;&nbsp;&nbsp;
<input name="Suite (enregistrer) >>" type="submit" class="arbo" value="Suite (enregistrer) >>">
</div>

<?php
	} 
	else {

	//////////////////////////////////////////////////////////////
	//////////////////       MODE EDITION       //////////////////
	////////////////// ZONE DE SAISIE DES INFOS ////////////////// 
	//////////////////////////////////////////////////////////////
	
	// this part determines the physical root of your website
	// it's up to you how to do this
	if (!preg_match('/\/$/msi', $_SERVER['DOCUMENT_ROOT']))
	  $_root = $_SERVER['DOCUMENT_ROOT'].'/';
	else
	  $_root = $_SERVER['DOCUMENT_ROOT'];
	
	define('DR', $_root);
	unset($_root);

?>
<style type="text/css">
  pre {
    background : #cccccc; 
    padding : 5 5 5 5;
  }
</style>

<span class="arbo2"><strong>Création du composant Graphique <?php echo $name; ?>:</strong></span>
<form name="createBriqueStep1" id="createBriqueStep1" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php if (strlen($_POST['id'])>0) echo $_POST['id']; ?>" method="post">
<input type="hidden" id="antislashr" name="antislashr"  value="\r" />
<span class="arbo">
<u><strong>Caractéristiques</strong></u>
<ul>
<li>type brique : Graphique</li>
<li>nom : <?php echo $name; ?></li>
<li>largeur : <?php echo $larg; ?></li>
<li>hauteur : <?php echo $long; ?></li>
</ul>
<br />
<div>
<?php
	// Reset des valeurs de sessions pour viter les conflits
	// Rcupration des valeurs passes en arguments (POST)
	// Ces valeurs sont enregistres dans la session
	if(!$_POST['init']==1 && !$_GET['init']==1 && sizeof($datas[0])==0){
		$_SESSION['HTMLcontent']='';
		$contenuhtml = preg_replace ( "/\`/","'",$_POST['contenuhtml']);
		$chart_id = extractChartData($contenuhtml);
	}
  
  	// Prparation de la variable $datas
	// elle contient tous les paramtres du flash
	// Rcupration des donnes prsentes dans une session
	$datas = unserialize( $_SESSION['chart_data'] );
	 if (is_array ($datas)) array_walk ($datas, "StripAllSlashes"); // Supprimer les slash rajouts par les posts
	$chart ['chart_type'] = $_SESSION['chart_type'];

	if($_POST['inverser_tableau']=="Inverser") {
		if(invert_tab($datas)) $datas=invert_tab($datas);
	}

	if($_POST['zonecopiercoller']!="") $datas = import_tabexcel($_POST['zonecopiercoller']);


	// Initialisation de la taille du tableau  afficher
	// avec le nombre de valeurs du tableau de donnes
	$xSize=sizeof($datas);
	$ySize=sizeof($datas[0]);

	// valeurs par dfaut du tableau de valeurs si cration de la brique
	// taille par dfaut 2x2
	if(!is_array($datas)||array_key_exists('',$datas[0])) { $xSize=3; $ySize=3; }

	// EDITION DU TABLEAU DE DONNEES

?>
<span>Type de graphique&nbsp;:&nbsp;<select name="chart_type">
  <option value="column" <?php echo ($chart['chart_type']=='bar') ? "selected" : ""?>>Histogramme</option>
  <option value="3d column" <?php echo ($chart['chart_type']=='3d column') ? "selected" : ""?>>Histogramme 3D</option> 
  <option value="stacked column" <?php echo ($chart['chart_type']=='stacked column') ? "selected" : ""?>>Histogramme en piles</option> 
  <option value="bar" <?php echo ($chart['chart_type']=='bar') ? "selected" : ""?>>Barres horizontales</option>
  <option value="stacked bar" <?php echo ($chart['chart_type']=='stacked bar') ? "selected" : ""?>>Barres horizontales en piles</option> 
  <option value="pie" <?php echo ($chart['chart_type']=='pie') ? "selected" : ""?>>Camembert</option>
  <option value="3d pie" <?php echo ($chart['chart_type']=='3d pie') ? "selected" : ""?>>Camembert 3D</option>
  <option value="area " <?php echo ($chart['chart_type']=='area ') ? "selected" : ""?>>Aires</option>
  <option value="stacked area" <?php echo ($chart['chart_type']=='stacked area') ? "selected" : ""?>>aires en piles</option> 
  <option value="line" <?php echo ($chart['chart_type']=='line') ? "selected" : ""?>>Graphique</option>
  <option value="candlestick" <?php echo ($chart['chart_type']=='candlestick') ? "selected" : ""?>>chandelles</option> 
</select></span><br /><br /><br />

Titre:<br />
<textarea id="titre" name="titre" style="width:400px; height:30px" class="SPAW_default_editarea" onclick="document.getElementById('importexcel').style.display=(event.shiftKey&&event.ctrlKey)?'':'none'"><?php echo $_SESSION['chart_title']; ?></textarea><br /><br /><br />


<div id="importexcel" style="display:none">
Zone pour copier/coller un tableau d'excel:<br />
<textarea id="zonecopiercoller" name="zonecopiercoller" style="width:400px; height:50px" class="SPAW_default_editarea"></textarea><br />
<input name="importer" value="Importer" type="submit" class="arbo"><br /><br /><br />
</div>


Dimensions du tableau (série x ordonnes):<br />
<input type="text" id="xSize" name="xSize" size="4" value="<?php echo $xSize-1; ?>"> x <input size="4" type="text" id="ySize" name="ySize" value="<?php echo $ySize-1; ?>"> <input name="resize" value="Redimensionner" type="submit" class="arbo"> <input name="inverser_tableau" value="Inverser" type="submit" class="arbo"><!--input type="button" name="resize" value="Redimensionner" onClick="javascript:window.location='<?php echo $_SERVER['PHP_SELF']; ?>?init=0;"--><br /><br />
<font class="arbo" style="color:#ff0000"><b><u>Note :</u> Si vous souhaitez faire passer le texte la ligne dans le tableau, il suffit d'ins&eacute;rer le code \r .</b></font><br /><br />
<table cellpadding="0" cellspacing="0" border="0" align="center">
 <tr>
  <td>Séries \ Ordonnes</td>
<?php
for($y=1;$y<$ySize;$y++) {
?>
  <td><input style="text-decoration: underline; text-align: center;" type="text" name="libelle<?php echo $y; ?>" value="<?php echo $datas[0][$y]; ?>"></td>
<?php
}
?>
 </tr>
<?php 
for($x=1;$x<$xSize;$x++) {
?>
  <td><input style="text-decoration: underline; text-align: center;" type="text" name="serie<?php echo $x; ?>" value="<?php echo $datas[$x][0]; ?>"></td>
<?php
	for($y=1;$y<$ySize;$y++) {
?>
  <td><input style="color: grey; text-align: right;" type="text" name="field<?php echo $x.$y; ?>" value="<?php echo $datas[$x][$y]; ?>"></td>
<?php
	}
?>
 </tr>
<?php
}
?>
</table><br /><br />

L&eacute;gende:<br />
<textarea id="legende" name="legende" style="width:400px; height:30px" class="SPAW_default_editarea"><?php echo $_SESSION['chart_legend']; ?></textarea><br /><br /><br />


Texte compl&eacute;mentaire:<br />
<textarea id="txtsupp" name="txtsupp" style="width:400px; height:50px" class="SPAW_default_editarea"><?php echo $_SESSION['chart_txt']; ?></textarea><br /><br /><br />

<input type="checkbox" id="hidedata" name="hidedata" value="hide" <?php echo ($_SESSION['chart_hidedata']=='hide') ? "checked" : ""?>> Masquer les donn&eacute;es sur le graphique<br /><br /><br />

</div>
<div align="center"><input name="preview" type="submit" class="arbo" value="Suite (preview) >>"><!--input name="Suite (preview) >>" type="button" class="arbo" onClick="alert('faire test de validit des donnes'); if(1==1) submit();" value="Suite (preview) >>"-->
  <br />
  <br />
</div>

<b><u>Note :</u></b> A cause de la zone r&eacute;serv&eacute;e à la saisie, l'espace de composition n'a pas forc&eacute;ment à la taille indiqu&eacute;e pr&eacute;c&eacute;demment.
Les tailles seront appliques d&eacute;finitivement à partir de l'&eacute;tape suivante (preview).</span>
<input type="hidden" name="pageEditor" value="<?php echo $_SERVER['REQUEST_URI'];?>">
<input type="hidden" name="TYPEcontent" value="Graphique">
<input type="hidden" name="l" value="<?php echo $larg; ?>">
<input type="hidden" name="h" value="<?php echo $long; ?>">
<input type="hidden" name="name" value="<?php echo $name; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="chart_id" value="<?php echo $chart_id; ?>">
</form>
<?php
	}
}
?>