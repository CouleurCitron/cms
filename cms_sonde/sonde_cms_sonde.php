<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/xml.parser.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/utils/htmlentities4flash.lib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/mail_lib.php');

header('Content-type: text/xml; charset=utf-8');
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?".">\n";
echo '<adequat>';
$debProcess = time();

$bAllPassed = true;

$rapport='sonde @ '.$_SERVER['HTTP_HOST'].'<br />'.date('Y-m-d H:m:s').'<br /><br />';

$aSondes = dbGetObjectsFromFieldValue3('cms_sonde', array('get_statut'), array('equals'), array(DEF_ID_STATUT_LIGNE), array('get_id'), array('ASC')) ;
if ((count($aSondes) > 0)&&($aSondes!=false)){
	foreach($aSondes as $sKey => $oSonde){		
	
		$xml = new SimpleXMLElement(readXMLfromURL($oSonde->get_sondeurl()));

		$result = $xml->xpath('/adequat/sonde');
		
		$node =$result[0];
		$attrs = $node->attributes();
		
		if (!isset($attrs['value'])){		
			//echo '<p>failed to probe</p>';
			$passed=false;
			$rapport.=$oSonde->get_nom().' / '.$attrs['name'].' => failed to probe<br />';
		}
		elseif(strval($attrs['value'])=="1"){		
			//echo '<p>test <strong>passed</strong></p>';	
			$passed=true;		
			$rapport.=$oSonde->get_nom().' / '.$attrs['name'].' => test passed<br />';
		}
		elseif(strval($attrs['value'])=="0"){		
			//echo '<p>test failed</p>';			
			$rapport.=$oSonde->get_nom().' / '.$attrs['name'].' => test failed<br />';
			$passed=false;
		}		
		
		if ($passed==false){
			$sSubject = 'sonde @ '.$_SERVER['HTTP_HOST'].' - '.date('Y-m-d H:m:s');
			$aAddies = array();
			
			if (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+$/', $oSonde->get_email())==1){
				$aAddies[]=$oSonde->get_email();
			}
			
			$oUser = new bo_users($oSonde->get_bo_users());
			if (preg_match('/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]+$/', $oUser->get_mail())==1){
				$aAddies[]=$oUser->get_mail();
			}
			
			foreach($aAddies as $k => $sAddy){
				multiPartMail($sAddy, $sSubject , $rapport , strip_tags($rapport), DEF_CONTACT_FROM, "", "", DEF_MAIL_HOST);
			}	
			
			$bAllPassed = false;
			$oSonde->set_lastres(0);
		}
		else{
			$oSonde->set_lastres(1);
			
		}
		
		$oSonde->set_lastrun(date('Y-m-d H:m:s'));
		dbSauve($oSonde);
		
	}
	
	if ($bAllPassed==true){
		echo '<sonde value="1" name="'.$_SERVER['HTTP_HOST'].'">all test passed</sonde>';
	}
	else{
		echo '<sonde value="0" name="'.$_SERVER['HTTP_HOST'].'">some tests failed</sonde>';
	}
}
else{
	echo '<sonde value="1" name="'.$_SERVER['HTTP_HOST'].'">host is up</sonde>';
}



$endProcess = time();
echo '<process time="'.($endProcess-$debProcess).'" />';
echo '</adequat>';  
?>