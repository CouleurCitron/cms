<html>
<body>

<?php
@header('X-UA-Compatible: IE=EmulateIE7');

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

$id = $_GET["id"]; 
$idField = $_GET["idField"];
$classeName = $_GET["classeName"];
$refer = $_GET["refer"];
$source = $_GET["source"];
$operation = $_POST["operation"];
eval("$"."oRes = new ".$classeName."(".$id.");"); 
 
eval("$"."file = "."$"."oRes->get_".$refer."();");  
eval("$"."serialize = "."$"."oRes->get_".$source."();"); 
 
$file = getValidResizedImage($file, '/custom/upload/'.$classeName.'/');  
if (is_file($_SERVER['DOCUMENT_ROOT']."/custom/upload/".$classeName."/".$file)) {  
//Calcul de la taille de l'image de fond
$sizeFond = getimagesize($_SERVER['DOCUMENT_ROOT']."/custom/upload/".$classeName."/".$file);
//Calcul largeur fenêtre
$widthWindow = $sizeFond[0]+10;
	
?>
<script src="/backoffice/cms/js/jquery.ui.core.js"></script>
<script src="/backoffice/cms/js/jquery.ui.widget.js"></script>
<script src="/backoffice/cms/js/jquery.ui.mouse.js"></script>
<script src="/backoffice/cms/js/jquery.ui.draggable.js"></script>	
<style>

.tab_drag {
	font-family: Arial,Helvetica,sans-serif;
	font-size: 12px;
	color: #000000;
	background-color: #EBEFF3;
}
.demo {
	border: 5px solid #447a95;
}
.demo div.fd {
	background:url('/custom/upload/<?php echo $classeName."/".$file; ?>' ) no-repeat top left;		
	z-index:-500;
	width: <?php echo $sizeFond[0]; ?>px;
	height: <?php echo $sizeFond[1]; ?>px;
}
.ajout_drag {
	padding: 15px 10px;	
}
.ajout_drag p {
	margin: 0px 0px 5px 0px;
}
.ajout_drag p label {
	color: #447a95;
    display: block;    
    font-weight: bold;
}
.ajout_drag p textarea { 
	border: 1px solid #447a95;
	background-color: #FFFFFF;
    font-family: Arial,Helvetica,sans-serif;
    font-size: 11px;
    padding: 2px;
    width: 100%;
}
.ajout_drag p a {
	display: block;
	float: left;
	margin-right: 5px;
	padding: 5px;
	background-color: #FFFFFF;
	border: 1px solid #447a95;
	color: #000000;
	font-weight: bold;
	text-decoration: none;
}
.ajout_drag p a:hover {
	color: #447a95;
}
.submit_drag {
	padding: 35px 10px 25px 10px;
	text-align: center;	
}
 
.submit_drag a {
	margin: 0px 5px;
	padding: 5px;
	background-color: #FFFFFF;
	border: 1px solid #447a95;
	color: #000000;
	font-weight: bold;
	text-decoration: none;
}
.submit_drag a:hover {
	color: #447a95;
}
.list_drag {
	padding: 0px 10px 20px 0px; 
}
.list_drag h2 {
	text-align: left;
	color: #447a95;
	font-size: 13px;
	text-transform: uppercase;
	padding: 15px 0px 20px 0px;
	margin: 0px;
	text-ident: 0px;
}
#newdraggable {
	display: none;
	margin: 5px 0px;
	border: 1px solid #ae3232;
	background-color: #FFFFFF;
	padding: 5px;
}
.currdraggable {
	cursor: pointer;
	border: 1px solid #000000;
	background-color: #f4f7f9;
	padding: 2px;	
	width: 25%;
	margin: 5px 0px;
}
.currdraggable2 {
	border: 1px solid #000000;
	background-color: #f4f7f9;
	padding: 5px;
	font-size: 11px;
	position: relative;
}
.currdraggable2 a {
	color: #000000;
}
.currdraggable2 a.update, .currdraggable2 a.del, .currdraggable2 a.validate {
	background: url(/backoffice/cms/img/2013/icone/modifier.png) no-repeat;
	padding-left: 16px;
	margin: 0px 5px;
}
.currdraggable2 a.del {
	background: url(/backoffice/cms/img/2013/icone/supprimer.png) no-repeat;
}
.currdraggable2 a.validate {
	background: url(/backoffice/cms/img/2013/icone/right.png) no-repeat;
}
.currdraggable2 input {
	background-color: #FFFFFF;
    border: 1px solid #447A95;
    font-family: Arial,Helvetica,sans-serif;
    font-size: 11px;
    padding: 2px;
    margin-right: 5px;
    /*width: 20%;*/
}
.b_legende {
	position: absolute;
	left: 10px;
	top: 25px;
	display: none;
	background-color: #000000;
	color: #FFFFFF;
	padding: 5px;
}
</style>


<script>
$(function() {
	var nblegende = 0;
	var $start_counter = $( "#event-start" ),
		$drag_counter = $( "#event-drag" ),
		$stop_counter = $( "#event-stop" ),
		counts = [ 0, 0, 0 ];
 
	nblegende = $("#maxlegende").val(); 
	$("#addlegende").click(function () {
		if ($("#addlabel_visible").val()!= '') { 
			nblegende++;
			var monContenu = '<div id="draggable'+ nblegende +'" class="currdraggable">'+$("#addlabel_visible").val()+' : '+$("#addlabel_invisible").val()+'<input type="hidden" id="labeldragvisible'+ nblegende +'" name="labeldragvisible'+ nblegende +'" value="'+addSlashes($("#addlabel_visible").val())+'" /><input type="hidden" id="labeldraginvisible'+ nblegende +'" name="labeldraginvisible'+ nblegende +'" value="'+addSlashes($("#addlabel_invisible").val())+'" /><input type="hidden" id="top'+ nblegende +'" name="top'+ nblegende +'" value="0" /><input type="hidden" id="left'+ nblegende +'" name="left'+ nblegende +'" value="0" /></div>';
			 
			$("#newdraggable").show();
			$("#newdraggable").append(monContenu);
			$("#draggable"+ nblegende +"" ).draggable({ 
				stop: function() {
					//alert($(this).position().left+ ' '+$(this).position().top);	 
					  
					$(this).children("#top"+nblegende).val(Math.round($(this).position().top));
					$(this).children("#left"+nblegende).val(Math.round($(this).position().left));
				}
			});
		}
		else {
			alert("il manque la légende");
		}
	});
});


function addSlashes (chaine) { 
	return chaine.replace(/\"/g,"__GUILL__");
	//return chaine;
}
function stripslashes (chaine) { 
	return chaine.replace(/__GUILL__/g,'&quot;');
	//return chaine;
}

function submitDrag()
{
	$("#myformdrag #operation").val("submitdrag"); 
	$("#myformdrag").submit();
}

function delDrag(nblegende)
{
	$("#labeldragvisible"+nblegende).val("");
	$("#labeldraginvisible"+nblegende).val("");
	$("#top"+nblegende).val(""); 
	$("#left"+nblegende).val(""); 
	$("#myformdrag #operation").val("submitdrag"); 
	$("#myformdrag").submit();
}

function updateDrag(nblegende)
{  
	var html = '<input type="text" id="labeldragvisible'+ nblegende +'" name="labeldragvisible'+ nblegende +'" value="'+stripslashes($("#labeldragvisible"+nblegende).val())+'" /><input type="text" id="labeldraginvisible'+ nblegende +'" name="labeldraginvisible'+ nblegende +'" value="'+stripslashes($("#labeldraginvisible"+nblegende).val())+'" /><input type="hidden" id="top'+ nblegende +'" name="top'+ nblegende +'" value="'+($("#top"+nblegende).val())+'" /><input type="hidden" id="left'+ nblegende +'" name="left'+ nblegende +'" value="'+($("#left"+nblegende).val())+'" /><a class="validate" href="#_" onClick="javascript:validateDrag('+nblegende+');" >validate</a>&nbsp;<a class="del" href="#_" onClick="javascript:delDrag('+nblegende+');" >del</a>';
	$("#draggable"+nblegende).html(html); 
}

function validateDrag(nblegende)
{ 
	//alert($("#labeldragvisible"+nblegende).val());
	//alert($("#labeldraginvisible"+nblegende).val());
	$("#labeldragvisible"+nblegende).val($("#labeldragvisible"+nblegende).val().replace(/\"/g,"__GUILL__"));
	$("#labeldraginvisible"+nblegende).val($("#labeldraginvisible"+nblegende).val().replace(/\"/g,"__GUILL__"));
	$("#myformdrag #operation").val("submitdrag");  
	$("#myformdrag").submit();
}

function reinitDrag()
{   
	$("#myformdrag").children("div").each(function(k){    
		$(this).html(""); 
	}); 
	$("#myformdrag #operation").val("submitdrag"); 
	$("#myformdrag").submit(); 
}
</script>
<form id="myformdrag" name="myformdrag" method="post" action="#" style="width: <?php echo $widthWindow; ?>px;">
<input type="hidden" id="operation" name="operation" value="submitdrag" />
<input type="hidden" id="source" name="source" value="<?php echo $source; ?>" />
<input type="hidden" id="idField" name="idField" value="<?php echo $idField; ?>" />
 
<?php

if ($_POST["operation"] == "submitdrag") { 
	
	
	$maxlegende = 0;
	$myTab = array();
	foreach ($_POST as $k => $value) {
		
		if (preg_match ("/^labeldragvisible[0-9]*/", $k)) {
			
			$nblegende = preg_replace ("/^labeldragvisible([0-9]*)/", "$1", $k);   
			
			if ($_POST["left".$nblegende]!= 0 && $_POST["top".$nblegende]!= 0) {
				$myTab[$nblegende] = array();
				$myTab[$nblegende]["title"] = $value;
				$myTab[$nblegende]["titleroll"] = $_POST["labeldraginvisible".$nblegende];
				$myTab[$nblegende]["top"] = $_POST["top".$nblegende];
				$myTab[$nblegende]["left"] = $_POST["left".$nblegende];
				
				if ($nblegende > $maxlegende) $maxlegende = $nblegende;
				
				echo '<style>
	
					#draggable'.$nblegende.' { 
					position:absolute;
					left:'.$malegende["left"].'px;
					top:'.$malegende["top"].'px;					
					}
					</style>
					
					<script> 
					 
					  $(document).ready(function() {						 
						  $("#show'.$nblegende.'").mouseover(function() {
							$("#roll'.$nblegende.'").show();
							$("#roll'.$nblegende.'").css("position", "absolute");
							$("#roll'.$nblegende.'").css("left", "10px");
							$("#roll'.$nblegende.'").css("top", "25px");	
						  });
						   $("#show'.$nblegende.'").mouseout(function() {
							$("#roll'.$nblegende.'").delay(500).hide();  
						  });
						});
					</script>
					';
					
					
				echo '
				<div id="draggable'.$nblegende.'" class="currdraggable2"><a href="#_" id="show'.$nblegende.'" >'.replaceGuillemet($value).'</a>
				<div id="roll'.$nblegende.'" class="b_legende">'.replaceGuillemet($_POST["labeldraginvisible".$nblegende]).'</div>								
				<input type="hidden" id="labeldragvisible'.$nblegende.'" name="labeldragvisible'.$nblegende.'" value="'.($value).'" />
				<input type="hidden" id="labeldraginvisible'.$nblegende.'" name="labeldraginvisible'.$nblegende.'" value="'.($_POST["labeldraginvisible".$nblegende]).'" />
				<input type="hidden" id="top'.$nblegende.'" name="top'.$nblegende.'" value="'.$_POST["top".$nblegende].'" />
				<input type="hidden" id="left'.$nblegende.'" name="left'.$nblegende.'" value="'.$_POST["left".$nblegende].'" />
				<a class="update" href="#_" onClick="javascript:updateDrag('.$nblegende.');" >update</a>&nbsp;
				<a class="del" href="#_" onClick="javascript:delDrag('.$nblegende.');" >del</a>				
				</div>';
			}
			
			
		}
		
		
	} 
	$serialiazeTab = serialize($myTab);
	echo '<input type="hidden" id="maxlegende" name="maxlegende" value="'.$maxlegende.'" />';
	eval("$"."oRes->set_".$source."('".$serialiazeTab."');"); 
	$r = dbSauve ($oRes); 
	
	echo "<script type=\"text/javascript\">\n";		
	//echo "alert(serialiazeTab_f".$idField.".value)"; 
			echo "serialiazeTab_f".$idField.".value= '".$serialiazeTab."';"; 
		echo "</script>\n"; 

}
else if ($serialize!=''){  
	 
	$myTab = unserialize(base64_decode($serialize)); 
	foreach ($myTab as $nblegende => $malegende) {
		  
		echo '<style>
	
			#draggable'.$nblegende.' { 
			position:absolute;
			left:'.$malegende["left"].'px;
			top:'.$malegende["top"].'px;			
			}
			
			</style>
			
			<script> 
			 
			  $(document).ready(function() {				 
				  $("#show'.$nblegende.'").mouseover(function() {
					$("#roll'.$nblegende.'").show();
					$("#roll'.$nblegende.'").css("position", "absolute");
					$("#roll'.$nblegende.'").css("left", "10px");
					$("#roll'.$nblegende.'").css("top", "25px");  
				  });
				   $("#show'.$nblegende.'").mouseout(function() {
					$("#roll'.$nblegende.'").delay(500).hide();  
				  });
				});
			</script>
			';
			
			
		echo '
		<div id="draggable'.$nblegende.'" class="currdraggable2"><a href="#_" id="show'.$nblegende.'" >'.replaceGuillemet($malegende["title"]).'</a>
		<div id="roll'.$nblegende.'"class="b_legende">'.replaceGuillemet($malegende["titleroll"]).'</div>		
		<input type="hidden" id="labeldragvisible'.$nblegende.'" name="labeldragvisible'.$nblegende.'" value="'.($malegende["title"]).'" />
		<input type="hidden" id="labeldraginvisible'.$nblegende.'" name="labeldraginvisible'.$nblegende.'" value="'.($malegende["titleroll"]).'" />
		<input type="hidden" id="top'.$nblegende.'" name="top'.$nblegende.'" value="'.$malegende["top"].'" />
		<input type="hidden" id="left'.$nblegende.'" name="left'.$nblegende.'" value="'.$malegende["left"].'" />
		<a class="update" href="#_" onClick="javascript:updateDrag('.$nblegende.');" >update</a>&nbsp;
		<a class="del" href="#_" onClick="javascript:delDrag('.$nblegende.');" >del</a>
		</div>';
	} 
	
	echo '<input type="hidden" id="maxlegende" name="maxlegende" value="'.newSizeOf($myTab).'" />';
	
}
else {
	echo '<input type="hidden" id="maxlegende" name="maxlegende" value="0" />';
}
 
?>
<table cellspacing="0" cellpadding="0" border="0" class="tab_drag">
	<tr>
		<td class="demo"><div class="fd"></div></td>		
	</tr>
	<tr>
		<td class="ajout_drag">
			<div id="newdraggable"><p>Veuillez prendre le(s) points çi-dessous et le(s) placer sur votre image çi-dessus : </p></div>
			<p>Pour ajouter un nouveau points et sa légende, remplir le formulaire çi-dessous : </p>
			<p><label>Texte visible :</label> <textarea rows="2" id="addlabel_visible" name="addlabel_visible" /></p>
			<p><label>Texte visible uniquement au roll :</label> <textarea rows="2" id="addlabel_invisible" name="addlabel_invisible" /></p>
			<p><a href="#_" id="addlegende">&raquo; Ajouter ce nouveau point</a></p>			
		</td>
	</tr>	
	<tr>
		<td class="submit_drag">			
			<a href="#_" onClick="javascript:submitDrag();" >&raquo; Enregistrer vos modifications</a> <a href="#_" onClick="javascript:reinitDrag();" >&raquo; Reinitialiser vos modifications</a>
		</td>
	</tr>
</table>
</form> 

<?php
}
?>
</body>
</html>


<?php

function replaceGuillemet( $chaine) {
	$chaine = str_replace ("__GUILL__", "&quot;", $chaine);
	
	return $chaine;
}
?>
