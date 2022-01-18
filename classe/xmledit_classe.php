<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

//init_set ('error_reporting', E_ALL);
	
// permet de dérouler le menu contextuellement
activateMenu("gestionclasse");

if (is_post("todo") == false){
	if (is_get("display") == true){
		
		$id = $_GET["display"];
		$oClass = new classe($id);
		$oEditedClassName = $oClass->get_nom();
		echo "edit XML for classe ".$oEditedClassName." (".$_GET["display"].")";
		if ($oClass->get_statut() == 4){
			echo ' | classe existante';
			$oEditedClass = new $oEditedClassName();
			$sXML = $oEditedClass->XML;
		}
		else{
			echo ' | nouvelle classe';
			$sXML = "";
		}
		//;
	}
	else{
		echo "no valid class id submitted";
	}
?>
<form id="xmlclass" name="xmlclass" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input id="id" name="id" value="<?php echo $id; ?>" type="hidden" />
<input id="todo" name="todo" value="sauve" type="hidden" />
<textarea id="fxml" name="fxml" class="textareaEdit">
<?php echo stripslashes(htmlentities($sXML)); ?>
</textarea><br />
<input id="freset" name="freset" type="reset" value="<?php $translator->echoTransByCode('btAnnuler'); ?>" />
<input id="fsubmit" name="fsubmit" type="submit" value="<?php $translator->echoTransByCode('btValider'); ?>" />
</form>
<?php
} // if (is_post("todo") == false){
else{
	if (is_post("id") == true) {

		$id = $_POST["id"];
		$oClass = new classe($id);
		$oEditedClassName = $oClass->get_nom();
		//$oEditedClass = new $oEditedClassName();
		
		echo "> Save XML for <b>".$oEditedClassName."</b> [ID: ".$_POST["id"]."] class<br/>\n";

		$postedXML = stripslashes($_POST["fxml"]);
		$stack = array();
		xmlStringParse($postedXML);
		
		if (is_array($stack) == true) {
			echo "> Given XML is valid<br/><br/>\n";

			$inherits = null;
			
			if (isset($stack[0]["attrs"]["INHERITS_FROM"])) {
				$inherits = $stack[0]["attrs"]["INHERITS_FROM"];

				echo "> <b>".$oEditedClassName."</b> class inherits from <b>".$inherits."</b> class.<br/>> <b>".$inherits."</b> class will be generated again from scratch.<br/><br/>\n";
			}

			$phpClass = generateClasseFromXMLString($postedXML);

			if (preg_match('/^job|cms|shp|nws[_\.]+/', $oEditedClassName) == 1) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/include/bo/class/'.$oEditedClassName.'.class.php')){
					unlink($_SERVER['DOCUMENT_ROOT'].'/include/bo/class/'.$oEditedClassName.'.class.php');
				}
				$phpClassFile = $_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/'.$oEditedClassName.'.class.php';
				dirExists('/include/cms-inc/class/'); // trivial
			}elseif ( (preg_match('/news[_\.]+/', $oEditedClassName) == 1) ){
				if(preg_match('/NEWSLETTER/',$_SESSION['fonct']) == 1 ){
					//classe CMS
					if (file_exists($_SERVER['DOCUMENT_ROOT'].'/include/bo/class/'.$oEditedClassName.'.class.php')){
						unlink($_SERVER['DOCUMENT_ROOT'].'/include/bo/class/'.$oEditedClassName.'.class.php');
					}
					$phpClassFile = $_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/'.$oEditedClassName.'.class.php';
					dirExists('/include/cms-inc/class/'); // trivial
				}else{
					//Classe non CMS ayant un mauvais préfixe (C'EST LE MAL !!)
					$phpClassFile = $_SERVER['DOCUMENT_ROOT'].'/include/bo/class/'.$oEditedClassName.'.class.php';
					dirExists('/include/bo/class/');					
				}			
			} else {
				$phpClassFile = $_SERVER['DOCUMENT_ROOT'].'/include/bo/class/'.$oEditedClassName.'.class.php';
				dirExists('/include/bo/class/');
			}
			// ecriture de la conf template
			$file = $phpClassFile;
		
			if (is_file($file)) {
				//récuperation de la partie PATCH si jamais il existe avant  d'écraser avec contenu new generation
				$pattern = "`/[\*] \[Begin patch\] [\*]/(.*)/[\*] \[End patch\] [\*]/`Us";
				preg_match($pattern, file_get_contents($file), $extract);

				//on modifie directement dedans
				$phpClass = preg_replace($pattern, $extract[0], $phpClass);
			}
		
			if (!$handle = fopen($file, 'w')) {
				 echo "<b>Impossible d'ouvrir </b>".$file;
				 exit;
			}

			//Write $somecontent to our opened file.
			if (fwrite($handle, $phpClass) === FALSE) {
				echo "<b>Impossible d'écrire dans </b>".$file;
				exit;
			}

			echo "> New content for ".$phpClassFile."<br />\n";
			pre_dump(htmlentities($phpClass));			
			
			//$statutRetour = "Ecriture avec succès dans le fichier de conf.";
			fclose($handle);

			if (!is_null($inherits)) {
				
				// on consertve les nodes de la classe héritante
				$aNodeToSort = $stack[0]["children"];
				
				//On supprime un héritage précedent éventuel
				$inheritedFile = $_SERVER['DOCUMENT_ROOT'].'/include/bo/class/'.$inherits.'.class.php';
				if (file_exists($inheritedFile))
					unlink($inheritedFile);
				
				// on récupère les nodes du XML de la classe héritée
				include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/'.$inherits.'.class.php');
				$inherited = new $inherits();
				$stack = array();
				xmlStringParse($inherited->XML);
				$inheritedNodes = $stack[0]["children"];				

				include_once($phpClassFile);
				$list_methods = get_class_methods($oEditedClassName);

				if (preg_match('/job|cms|shp|nws|newsletter|news[_\.]+/', $inherits) == 1) {
					if (!file_exists($inheritedFile) || (filesize($inheritedFile)==0))
						copy($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/class/'.$inherits.'.class.php', $inheritedFile);
					//chmod($inheritedFile, 0777);
				}

				// on récupère le contenu du fichier de la classe héritée
				$file = file_get_contents($inheritedFile);
				// on modifie directement dedans
				$file = str_replace("var \$inherited_list = array();", "var \$inherited_list = array('".$oEditedClassName."');",$file);
				
				
				
				// on passe toutes les méthodes enfant mais on ne récupère que les set et get spécifiques,
				// de plus on ne prend pas en compte les set_id() et get_id()
				$field_ok_pile = Array();
				foreach ($list_methods as $key=>$meth) {
					if (((substr($meth,0,4) == 'set_') || (substr($meth,0,4) == 'get_')) && substr($meth,0,7 ) != 'set_id' && substr($meth,0,7 ) != 'get_id' ){

						// Pour les méthodes restantes, on teste si on a un équivalent dans les méthodes heritées :
						// si oui : on ne fait rien
						// si non : on ajoute
						if (strpos($file, 'function '.$meth.'(') === FALSE) {
							$test = substr($meth, 4);
							for ($i=0; $i<count($aNodeToSort); $i++) {
								// var_dump( ($aNodeToSort[$i]["attrs"]["NAME"] == substr($meth,4)) ).' -> '.var_dump(!in_array(substr($meth,4),$methods_done)).'<br>';
								// pre_dump($methods_done);
								if ($aNodeToSort[$i]["attrs"]["NAME"] == $test && !in_array($tst, array_keys($field_ok_pile))) {
									$txt_xml = '';
									foreach ($aNodeToSort[$i]["attrs"] as $key=>$val) {
										if ($key != 'INHERIT_POSITION')
											$txt_xml .= strtolower($key).'="'.$val.'" ';
									}
									$field_ok_pile[$test] = Array(	'position'	=> $aNodeToSort[$i]["attrs"]['INHERIT_POSITION'],
													'item'		=> '<item '.$txt_xml.'/>');
								}
							}
						}
					}
				}
				//viewArray($field_ok_pile);

				// on commence à reconstruire le XML de la classe héritée
				$inheritedXML = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
				$inheritedProps = '';
				foreach ($stack[0]["attrs"] as $key=>$val)
					$inheritedProps .= ' '.strtolower($key).'="'.$val.'"';
				$inheritedXML .= "\n".'<class'.$inheritedProps.'>';
				
				// on épluche les champs de la classe héritée
				$items_done = array();
				$methods_done = array();
				for ($i=0; $i<count($inheritedNodes); $i++) {
					$inheritedProps = '';
					foreach ($inheritedNodes[$i]["attrs"] as $key=>$val)
						$inheritedProps .= strtolower($key).'="'.$val.'" ';
					$inheritedXML .= "\n".'<item '.$inheritedProps.'/>';

					// on fait le tour des champs OK à ajouter
					// pour créer les methodes et on intercale les nouveaux champs dans le xml de le classe héritée
					foreach ($field_ok_pile as $field=>$props) {
						if (!array_key_exists($field, $items_done) && $inheritedNodes[$i]["attrs"]['NAME'] == $props['position']) {
							// on insère les item de la classe héritante
							$inheritedXML .= "\n".$props['item'];
							$items_done[$field] = true;
						}
						if (!array_key_exists($field, $methods_done)) {
							// On ajoute les méthodes
							$file = str_replace("// setters","// setters\nfunction set_".$field."(\$c_inherited_".$field."){ return(\$this->inherited['".$oEditedClassName."']->set_".$field."(\$c_inherited_".$field.")); }",$file);
							$file = str_replace("// getters","// getters\nfunction get_".$field."(){ return(\$this->inherited['".$oEditedClassName."']->get_".$field."()); }",$file);
							$methods_done[$field] = true;
						}						
					}
				}
				// on vérifie les positions champ invalides ou non définies
				// ils seront placés en fin de liste
				foreach ($field_ok_pile as $field=>$props) {
					if (!array_key_exists($field, $items_done))
						$inheritedXML .= "\n".$props['item'];
				}
				// on clôture le XML de la classe héritée
				$inheritedXML .= "\n".'</class>';
				$file = str_replace('var $XML_inherited = null;', 'var $XML_inherited = "'.str_replace('"','\"',$inheritedXML).'";', $file);
			
				// ecriture de la conf template
				if (!$handle = fopen($inheritedFile, 'w')) {
					echo "Impossible d'ouvrir ".$inheritedFile;
					exit;
				}
				//Write $somecontent to our opened file.
				if (fwrite($handle, $file) === FALSE) {
					echo "Impossible d'écrire dans ".$inheritedFile;
					exit;
				}
				fclose($handle);
			}
		} else	echo "le XML est invalide<br />\n";

	} else	echo "no valid class id submitted";

}
?>