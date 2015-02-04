<?php

/**
 * Page permettant d'ordonner une liste COMPLETE d'éléments d'une classe donnée.
 * 
 * 22/10/2013 : Raphaël
 */

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');


//pre_dump($_SESSION["sqlpag"]); die();

global $champsDisplay;
global $displayField;
global $champsParent;
global $stack;
global $classe;
global $translator;
global $translate;
global $depth;

$translator =& TslManager::getInstance();

$classe = $_GET['classe']; 

//die($classe);

// permet de dérouler le menu contextuellement
if (!isset($classeName)){
    $classeName = preg_replace('/[^_]*_(.*)\.php/', '$1', basename($_SERVER['PHP_SELF']));
}
if (function_exists('activateMenu')){
    activateMenu('gestion'.$classeName);
} 


/* On parse le xml */
eval("$"."oRes = new ".$classe."();");
if(!is_null($oRes->XML_inherited))
        $sXML = $oRes->XML_inherited;
else
        $sXML = $oRes->XML;
//$sXML = $oRes->XML;
unset($stack);
$stack = array();
xmlClassParse($sXML);

//$aItems = dbGetObjects($classe);

$champsDisplay = $stack[0]["attrs"]["DISPLAY"];

foreach($stack[0]['children'] as $k => $fields){
    //pre_dump($fields);
    if($fields['attrs']['NAME'] == $champsDisplay){
        $translate = false;
        if($fields['attrs']['TRANSLATE'] == "reference"){
            $translate = true;
        }
    }
}



/* profondeur de la liste */
if(isset($stack[0]["attrs"]["DEPTH"])){
    $depth = (int)$stack[0]["attrs"]["DEPTH"];
} else {
    $depth = 1;
}

if($depth > 1){
    if(isset($stack[0]["attrs"]["PARENT"]) && $stack[0]["attrs"]["PARENT"] != ""){
        $champsParent = $stack[0]["attrs"]["PARENT"];
    } else {
        die("Erreur, il est nécessaire d'avoir un champs parent.");
    }
    
}


//pre_dump($translate);

//pre_dump($aItems);

//pre_dump($stack);

function constructList($idParent="", $stack){
   

    $sqlClassSession = $_SESSION["classeName"];
    if($sqlClassSession == $_GET['classe'])
        $sSqlSearch = $_SESSION["sqlpag"];


    $asSqlSearch = explode("ORDER BY", $sSqlSearch);

    //WHERE NON EXISTANT
    $where_exists = explode("WHERE", $sSqlSearch);
    if(count($where_exists) == 1) {
        $asSqlSearch[0] = $asSqlSearch[0]." WHERE 1 = 1 ";
    }

    //$sSqlSearch = "SELECT DISTINCT shp_gamme.*  FROM shp_gamme WHERE  shp_gamme.shp_gam_id_site=1  ORDER BY shp_gam_id_gamme ASC, shp_gam_ordre ASC";

    global $champsDisplay;
    global $displayField;
    global $champsParent;
   // global $stack;
    global $classe;
    global $translate;
    global $depth;

    if($champsParent==''){
        $sSqlSearch = $asSqlSearch[0]."ORDER BY ".$classe.".".$stack[0]["attrs"]["PREFIX"]."_".$stack[0]["attrs"]["ORDONABLE"]." ASC, ".$classe.".".$stack[0]["attrs"]["PREFIX"]."_id ASC";

    } else {
        $sSqlSearch = $asSqlSearch[0]."ORDER BY ".$classe.".".$stack[0]["attrs"]["PREFIX"]."_$champsParent ASC, ".$classe.".".$stack[0]["attrs"]["PREFIX"]."_".$stack[0]["attrs"]["ORDONABLE"]." ASC, ".$classe.".".$stack[0]["attrs"]["PREFIX"]."_id ASC";    
    }




    //pre_dump($sSqlSearch);
    //pre_dump($stack);
    
    $translator =& TslManager::getInstance();
    
    if(!isset($sSqlSearch) || (isset($sSqlSearch) && $idParent != "") ){
        //pre_dump($idParent);
        if($idParent == ""){
            //pre_dump("idParent vide");
            if($depth >= 1){
                //pre_dump($depth);
                $aItems = dbGetObjectsFromFieldValue3($classe, array("get_$champsParent"), array("lower"), array("1"), array("get_".$stack[0]["attrs"]["ORDONABLE"], "get_id"), array("ASC", "ASC"));
            } else {
                //pre_dump("normal");
                $aItems = dbGetObjectsFromFieldValue2($classe, array(), array(), array("get_".$stack[0]["attrs"]["ORDONABLE"], "get_id"), array("ASC", "ASC"));
            }
        } else{
            //pre_dump("children");
            //$aItems = dbGetObjectsFromFieldValue2($classe, array("get_$champsParent"), array($idParent), array("get_".$stack[0]["attrs"]["ORDONABLE"], "get_id"), array("ASC", "ASC"));
			$aItems = dbGetObjectsFromFieldValue3($classe, array("get_$champsParent"), array("equals"), array($idParent), array("get_".$stack[0]["attrs"]["ORDONABLE"], "get_id"), array("ASC", "ASC"));			
        }
    } else {
        if($champsParent==''){
            $sSql = $sSqlSearch;
        } else {
            $sSql = str_replace("WHERE ", "WHERE ".$classe.".".$stack[0]["attrs"]["PREFIX"]."_$champsParent < \"1\" AND ", $sSqlSearch);
        }

        $aItems = dbGetObjectsFromRequete($sqlClassSession, $sSql);

    }

    //pre_dump($aItems); die();
    
    $sHtml = "";
	
    
    if($idParent && count($aItems)){
       $sHtml .= '<ol>'; 
    }
    foreach($aItems as $k => $oItem){
        if( (isset($sSqlSearch) && $idParent <= 1 ) || $idParent >= 1){
            //pre_dump($champsDisplay);
            //pre_dump($oItem);
            eval("$"."displayField = $"."oItem->".$champsDisplay.";");
            //pre_dump($displayField);
            $sHtml .= '<li id="item_'.$oItem->get_id().'">
                <div>
                    '.$oItem->get_id().' - ';
            if(!$translate) $sHtml .= strip_tags($displayField); 
            else $sHtml .= strip_tags($translator->getById($displayField));
            $sHtml .=    '</div>

            ';
            if($champsParent!=''){
                $sHtml .=    constructList($oItem->get_id(), $stack);
            }            
            $sHtml .=    '</li>';
        }
        
    }
    if($idParent && count($aItems)){
       $sHtml .= '</ol>'; 
    }
    
    return $sHtml;
    
    
}

//pre_dump($stack[0]["attrs"]);
$ordonable_champs = $stack[0]["attrs"]["ORDONABLE"];

?>
<ol class="sortablelist">
    <?php echo constructList("", $stack); ?>
</ol>


<script>
     $(document).ready(function(){

        $('.sortablelist').nestedSortable({
            handle: 'div',
            items: 'li',
            toleranceElement: '> div',
            maxLevels: <?php echo $depth; ?>,
//            change: function(serialized){
//                //alert('ok');
//                
//                console.log($('.sortablelist').serialized);
//            }
            update: function () {
                list = $(this).nestedSortable('serialize', {startDepthCount: 0});
                classData = '<?php echo $classe; ?>';
                field = '<?php echo $ordonable_champs; ?>';
                //console.log(list);
                
                $.ajax({
                        type		: 'POST',
                        url		: '/include/cms-inc/autoClass/list.saveolorder_item.php', //sauvegarde de l'ordre
                        data		: { item: list, classe: classData, ordered: field, depthData: <?php echo $depth; ?>, fieldparent: '<?php echo $champsParent; ?>' },
                        dataType	: 'html',
                        success		: function ( donnees ) { // si la requête est un succès
                        },
                        error		: function (donnees){
                                alert('une erreur est survenue, veuillez contacter votre administrateur');
                        }
                });
                
                
            }
        });
        
        

    });
</script>