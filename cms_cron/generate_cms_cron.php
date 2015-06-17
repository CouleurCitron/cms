<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php'); 

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php'); 

activateMenu("gestionclasse"); 

?>


<div class="ariane"><span class="arbo2">MODULE >&nbsp;</span><span class="arbo3">Génération du fichier Crontab</span></div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="savePage" class="arbo">
	<input type="hidden" name="node_id" id="node_id" value=""/>
	<div id="virginie">
	<p> <br />Pour éditer une crontab : <br /><br />
	<input type="text" value="EDITOR=ee crontab -e" /></p>
	<p> <br />
	Pour connaitre les taches programmées faire : <br /><br />
	<input type="text" value="crontab -e" /></p>
 	<p>
	  <br />
		Copier coller : <br /> <br />
	  <textarea name="description" cols="60" rows="10" class="arbo" id="form_description"><?php echo getAllCronTab(); ?></textarea>
	  </p>
 <p> <br /> <br />Le format de la crontab est le suivant : mm hh jj MMM JJJ tâche<br /><br />

* mm représente les minutes (de 0 à 59)<br />
* hh représente l'heure (de 0 à 23)<br />
* jj représente le numéro du jour du mois (de 1 à 31)<br />
* MMM représente le numéro du mois (de 1 à 12) ou l'abréviation du nom du mois (jan, feb, mar, apr, …)<br />
* JJJ représente l'abréviation du nom du jour ou le chiffre correspondant au jour de la semaine (0 représente le dimanche, 1 représente le lundi, ..)<br />
* tâche représente la commande ou le script shell à exécuter<br />
</p>
 <p>  <br /><br />Pour chaque unité de temps (minute/heure/…) les notations sont possibles:<br /> <br />

* * : à chaque unité de temps (0, 1, 2, 3, 4…)<br />
* 5,8 : les unités de temps 5 et 8<br />
* 2-5 : les unités de temps de 2 à 5 (2, 3, 4, 5)<br />
* */3 : toutes les 3 unités de temps (0, 3, 6, 9…)<br />
* 10-20/3 : toutes les 3 unités de temps, entre la dixième et la vingtième (10, 13, 16, 19)
</p>
 <p>  <br /><br /> Par exemple exécution d'un script php toutes les 30 minutes :<br />

*/30 * * * * /usr/local/bin/php -f /home/k1001/scripts/test.php\\<br /><br />

Exécution d'un script php (via http) tous les jours ouvrés à 11h 15 sans conserver la sortie :<br />

15 11 * * 1-5 /usr/bin/fetch -o /dev/null http://www.monsite.com/test.php<br /><br />

Le même en envoyant la sortie par mail :<br />

15 11 * * 1-5 /usr/bin/fetch -o - http://www.monsite.com/test.php | mail -s "Mon Cron" moi@mondomaine.com<br /><br />

En cas d'erreur vous trouverez les informations nécessaires en consultant (par exemple dans le webmail), <br />
les mails de l'utilisateur en question (dans notre exemple k1001). <br />
</p>
	</div> 
</form>


<?php

function getCronTab ($id_cron) {
	
	
	 
	if (isObjectById("cms_cron", $id_cron)	) {
	
		$oObj = getObjectById("cms_cron", $id_cron);
		 
		$cron = $oObj->get_mm().' '.$oObj->get_hh().' '.$oObj->get_jj().' '.$oObj->get_mmm().' '.$oObj->get_jjj().' /usr/bin/fetch http://'.$_SERVER['HTTP_HOST'].''.$oObj->get_file();
		
		return $cron;
		
	}
	else {
	
		return $false;
		
	}
		 
}


function getAllCronTab () {
	 
	$aObj = dbGetObjectsFromFieldValue2("cms_cron", array('get_statut'),  array(DEF_ID_STATUT_LIGNE), array('get_ordre'), array('ASC'));
	if (sizeof($aObj) > 0 && $aObj != false) {
		
		$cron = '';
		
		foreach ($aObj as $oObj ) {
			$cron.= getCronTab($oObj->get_id())."\n";
		} 
		return $cron;
		
	}
	else {
	
		return $false; 
	}
		 
}

?>