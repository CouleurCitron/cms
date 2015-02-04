<?php

/**
 * Page permettant de générer une arborescence physique sur le serveur à partir d'une classe récursive
 * 
 * 02/12/2014 : Guillaume
 */

include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/autoClass/lib.inc.php');

global $stack;
global $classe;
global $translator;

$translator =& TslManager::getInstance();

$classe = $_GET['classe'];

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
//conient la référence aux dossiers
$countfoldersCreated = 0;

//données post + traitement
if(isset($_POST["fDir"])) {
    $directory = $_POST["fDir"];
    $foldersCreated = array();
    $foldersCreated["-1"] = $_POST["fDir"];
    recursiveCreateFolders('-1', $foldersCreated, $countfoldersCreated);
}

function recursiveCreateFolders($parent = '-1', $foldersCreated, &$countfoldersCreated) {
    $aFolders = dbGetObjectsFromFieldValue2("luz_regafffolder", array('get_statut', 'get_dossier'), array(DEF_ID_STATUT_LIGNE, $parent), array('get_ordre'), array('ASC'));
    $delete_existing = isset($_POST["fDelete"]) ? true : false;
    foreach($aFolders as $aFolder){
        $folder_id = $aFolder->get_id();
        $folder_titre = $aFolder->get_titre();
        if(!isset($foldersCreated[$parent])) {
            break;
        } else {
            $directory = $foldersCreated[$parent];
            if(substr($directory, -1) != '/') {
                $directory.='/';
            }
            $folder_path = $directory.$folder_id.'-'.removeForbiddenChars(trim($folder_titre));
            if($delete_existing && is_dir($folder_path)) {
                foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder_path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                    $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
                }
                rmdir($folder_path);
            }
            $created = mkdir($folder_path, 0777, true);
            if($created) {
                $foldersCreated[$folder_id] = $folder_path;
                $countfoldersCreated++;
            }
            recursiveCreateFolders($folder_id, $foldersCreated, $countfoldersCreated);
        }
    }
}

?>

<form id="arboPath" name="arboPath" method="post" action="<?php echo $_SERVER["REQUEST_URI"] ?>">
    <table width="500" border="0" align="center" cellpadding="3" cellspacing="3" class="arbo">
        <tbody>
            <tr class="ligne">
                <td class="arbo"><strong>Chemin absolu sur le serveur&nbsp;:</strong><br></td>
                <td class="arbo">
                    <textarea class="arbo" cols="40" rows="6" id="fDir" name="fDir"><?php echo isset($_POST["fDir"]) ? $_POST["fDir"] : $_SERVER['DOCUMENT_ROOT'] ?></textarea>
                </td>
            </tr>
            <tr class="ligne">
                <td class="arbo"><strong>Supprimer les dossiers existants&nbsp;:</strong><br></td>
                <td class="arbo">
                    <?php  $delete_existing = isset($_POST["fDelete"]) ? true : false; ?>
                    <input type="checkbox" name="fDelete" value="true" <?php echo $delete_existing ? 'checked' : '' ?>><br>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div align="center" class="arbo">
        <p>
            <input type="button" class="arbo" value="Générer" onclick="javascript:submitPath()">
        </p>
    </div>  
    <div align="center" class="arbo">
        <p>
            <?php 
                if(isset($_POST["fDir"])) {
                    echo $countfoldersCreated.' dossiers ont été créés';
                }
            ?>
        </p>
    </div>  
</form>

<script type="text/javascript">
    function submitPath() {
        $('#arboPath').submit();
    }
</script>
