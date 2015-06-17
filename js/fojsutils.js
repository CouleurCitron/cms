function init(){
  checkIframe();
  
  // Prevent form/ajax double submitting
	// usage : $('#my_form').preventDoubleSubmit();
	jQuery.fn.preventDoubleSubmit = function () {
		var alreadySubmitted = false;
		return jQuery(this).submit(function () {
			if (alreadySubmitted)
				return false;
			else	alreadySubmitted = true;
		});
	};

}
function reinit(){
  checkIframe();
}

function getIsCrawler(){
	var crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|'+'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|'+'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|'+'Lynx'; // Lynx est inclut pour tester
	
	Expression = new RegExp(crawlers,"i");	
	if(Expression.test(navigator.userAgent)) {
		return true;
	}
	else {
		return false;
	}	
}

if (getIsCrawler()==false){
	window.onload = init;
	window.onresize = reinit;
}

/* à inclure dans /custom si voulu
if (navigator.appVersion.indexOf("MSIE")>=0){
	var arVersion = navigator.appVersion.split("MSIE")
	var version = parseFloat(arVersion[1])
	if (version < 7){
		window.attachEvent("onload", correctPNG);
	}
}*/



function launchEditor(id, mode, page){
	if (mode == undefined){
		mode = "page"; // page ou popup
	}
	
	if (id == -1){
		if (mode == "popup"){
			//MM_openBrWindow('/frontoffice/slideshow/index.php','editor','status=yes,width=990,height=580');
			MM_openBrWindow('/frontoffice/slideshow/index.php','editor','status=yes,width='+screen.width+',height='+screen.height);
			if (page!=null){
				document.location.href=page;
			}
		}
		else{
			document.location.href='/frontoffice/slideshow/index.php';
		}
	}
	else{
		if (mode == "popup"){
			//MM_openBrWindow('/frontoffice/slideshow/slideshow.php?id='+id,'editor','status=yes,width=990,height=580');
			MM_openBrWindow('/frontoffice/slideshow/slideshow.php?id='+id,'editor','status=yes,width='+screen.width+',height='+screen.height);
			if (page!=null){
				document.location.href=page;
			}
		}
		else{
			document.location.href='/frontoffice/slideshow/slideshow.php?id='+id;
		}
	}
}

function correctPNG() {// correctly handle PNG transparency in Win IE 5.5 & 6.
  var arVersion = navigator.appVersion.split("MSIE");
  var version = parseFloat(arVersion[1]);
  if ((version >= 5.5) && (document.body.filters)){
     for(var i=0; i<document.images.length; i++){
        var img = document.images[i]
        var imgName = img.src.toUpperCase()
        if (imgName.substring(imgName.length-3, imgName.length) == "PNG"){
           var imgID = (img.id) ? "id='" + img.id + "' " : ""
           var imgClass = (img.className) ? "class='" + img.className + "' " : ""
           var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
           var imgStyle = "display:inline-block;" + img.style.cssText
           if (img.align == "left") imgStyle = "float:left;" + imgStyle
           if (img.align == "right") imgStyle = "float:right;" + imgStyle
           if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle
           var strNewHTML = "<span " + imgID + imgClass + imgTitle
           + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
           + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
           + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>"
           img.outerHTML = strNewHTML
           i = i-1
        }
     }
  }
}

function checkemail(str){
	var filter=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(str)){
		return true;
	}			
	else{
		return false;
	}
}

function go_to_rubrik(idSelect, NomObjet, pagePhp){	
	for (i = 0; i < document.getElementById(idSelect).options.length; i++) {
		if (document.getElementById(idSelect).options[i].selected){
			if (document.getElementById(idSelect).options[i].value != ""){
				document.location.href = pagePhp+"?id="+document.getElementById(idSelect).options[i].value;		
			 }
		}
	}
}

function printBrique(id){
	window.open('/modules/print.php?id=' + id,'print','scrollbars=yes,resizeable=yes,menubar=no');
}

function checkIframe(){
//	if(window.document.title!='glossaire' || !window.opener) { // on est dans une popup glossaire => pas de cadre!!!
		// on est en frontoffice		
		/* PAS POUR OREDON		
		if (this.document.location.href.indexOf("/content/") > 0){
			if (this.name == "awsiframe"){
					//todo bene
			}
			else{
				window.top.document.location.href = "/content/oramip/inter.php?ifile=/"+getAccessPath();
			}
		} else {
			// on est en backoffice
			}			
		PAS POUR OREDON */
	//	}
	if (this.document.location.href.indexOf("/backoffice/") == -1){
		brique(); //SID ***
	}
}

function getAccessPath(){
	var l_adresse = document.location.href;
	//on vire protocole ://
	l_adresse = l_adresse.substr(l_adresse.indexOf("://")+3);
	return l_adresse.substr(l_adresse.indexOf("/")+1);
}

// recherche
function update(obj) {
	if((obj.value=='Rechercher dans le site') || (obj.value=='rechercher dans le site')  || (obj.value=='Search')  || (obj.value=='Rechercher') || (obj.value=='votre e-mail'))
		obj.value='';
}
function searchword() {
	if(document.getElementById('searchfield').value!='')
		openBrWindow('/frontoffice/recherche/result.php?keyword=' + document.getElementById('searchfield').value + "&site=" + document.getElementById('fsite').value,'searchWnd',620,400,'scrollbars=yes','true')
}
function searchwordSelf() {
	if ((document.getElementById('keyword').value!='') && (document.getElementById('recherche')!=undefined)){
		//document.getElementById('recherche').submit();
		$('#recherche').submit()	;	
	}
}
function dosearchfield(eventKeyCode) {
	if(eventKeyCode==13){
		searchword();
	}
}

// Ouverture du popup
function send(id) {
	srcHTML = window.opener.document.getElementById(id).innerHTML;
	document.getElementById('contenu').innerHTML = srcHTML;
}

function impression(id) {
	srcHTML = window.opener.document.getElementById(id).innerHTML;
	document.getElementById('contenu').innerHTML = srcHTML;
	window.print();
}

function printIt(obj) {
	divObj = getDivToProcess(obj);
	if(divObj!=null) {
		window.open('/modules/print.php?id=' + divObj.getAttribute('id'),'print','scrollbars=yes,resizeable=yes,menubar=no');
	} else {
		window.print();
	}
}

function sendIt(obj) {
	divObj = getDivToProcess(obj);
	if(divObj!=null) {
		window.open('/modules/sendfriend.php?id=' + divObj.getAttribute('id'),'send','scrollbars=yes,resizeable=yes,menubar=no');
	} else {
		window.alert('Fonctionnalité désactivée sur la page d\'impression');
	}
}

function getDivToProcess(obj) {
	val = null;
	while(obj.tagName!='div' && obj.tagName!='DIV' && obj.tagName!='body' && obj.tagName!='BODY') {
		obj = obj.parentNode;
	}
	id = obj.getAttribute('id');
	if (id == "" || id == null){
		return getDivToProcess(obj.parentNode);
	}
	
	if(id.match(/^div/) || id.match(/^bf/)) {
		val = obj;
	}
	return val;
}

function openBrWindow(theURL,winName,width,height,features,bCentre) {
  var window_width = width;
  var window_height = height;
  var newfeatures= "," + features;
  var window_top = (screen.height-window_height)/2;
  var window_left = (screen.width-window_width)/2;
	if(bCentre == 'false'){
		newWindow=window.open(''+ theURL + '',''+ winName +
'','width=' + window_width + ',height=' + window_height + newfeatures +
'');
	}else{
	  newWindow=window.open(''+ theURL + '',''+ winName +
'','width=' + window_width + ',height=' + window_height + ',top=' +
window_top + ',left=' + window_left + newfeatures + '');
	}
  newWindow.focus();
}
// Gestion des RollOvers
function img_RollOver(objSel, imgUrl){
	objSel.src = imgUrl;
}

// Gestion des RollOuts
function img_RollOut(objSel, imgUrl){
  objSel.src = imgUrl;
}	

// Gestion des RollOvers
function img_rollOver(objSel, imgUrl){
	objSel.src = imgUrl;
}

// Gestion des RollOuts
function img_rollOut(objSel, imgUrl){
  objSel.src = imgUrl;
} 
	
// Gestion du preload
function img_Preload(){
	var d=document;
	if(d.image){
		if(!d.tbImage) d.tbImage = new Array();
		var i,j=d.tbImage.length, a=img_preload.arguments;
		for(i=0;i<a.length;i++){
			if(a[i].indexOf("#")!=0){
					d.tbImage[j] = new Image;
					d.tbImage[j++].src=a[i];
			}
		}
	}
}

// les fonctions Js de dreamweaver
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
   // si pas de value -> pas de lien
   if (selObj.options[selObj.selectedIndex].value != "") {
       eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
       if (restore) selObj.selectedIndex=0;
   }
} 
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function verifyCompatibleBrowser(){ 
    this.ver=navigator.appVersion 
    this.dom=document.getElementById?1:0 
    this.ie5=(this.ver.indexOf("MSIE 5")>-1 && this.dom)?1:0; 
    this.ie4=(document.all && !this.dom)?1:0; 
    this.ns5=(this.dom && parseInt(this.ver) >= 5) ?1:0; 
 
    this.ns4=(document.layers && !this.dom)?1:0; 
    this.bw=(this.ie5 || this.ie4 || this.ns4 || this.ns5) 
    return this 
} 
bw=new verifyCompatibleBrowser() 
 
 
var speed=50 
 
var loop, timer 
 
function ConstructObject(obj,nest){ 
    nest=(!nest) ? '':'document.'+nest+'.' 
    this.el=bw.dom?document.getElementById(obj):bw.ie4?document.all[obj]:bw.ns4?eval(nest+'document.'+obj):0; 
    this.css=bw.dom?document.getElementById(obj).style:bw.ie4?document.all[obj].style:bw.ns4?eval(nest+'document.'+obj):0; 
    this.scrollHeight=bw.ns4?this.css.document.height:this.el.offsetHeight 
    this.clipHeight=bw.ns4?this.css.clip.height:this.el.offsetHeight 
    this.up=MoveAreaUp;this.down=MoveAreaDown; 
    this.MoveArea=MoveArea; this.x; this.y; 
    this.obj = obj + "Object" 
    eval(this.obj + "=this") 
    return this 
} 
function MoveArea(x,y){ 
    this.x=x;this.y=y 
    this.css.left=this.x 
    this.css.top=this.y 
} 
 
function MoveAreaDown(move){
	if(this.y>-this.scrollHeight+objContainer.clipHeight){
    this.MoveArea(0,this.y-move) 
    if(loop) setTimeout(this.obj+".down("+move+")",speed) 
	} 
} 
function MoveAreaUp(move){ 
	if(this.y<0){ 
    this.MoveArea(0,this.y-move) 
    if(loop) setTimeout(this.obj+".up("+move+")",speed) 
	} 
} 
 
function PerformScroll(speed){ 
	if(initialised){ 
		loop=true; 
		if(speed>0) objScroller.down(speed) 
		else objScroller.up(speed) 
	} 
} 
function CeaseScroll(){ 
    loop=false 
    if(timer) clearTimeout(timer) 
} 

var initialised; 
function InitialiseScrollableArea(strContent, strContainer){ 
    objContainer= new ConstructObject(strContainer); 
    objScroller=new ConstructObject(strContent, strContainer) 
    objScroller.MoveArea(0,0) 
    objContainer.css.visibility='visible' 
    initialised=true; 
} 

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}
function quizz(id) { 
if(id==3) window.opener.document.location='/content/situation%20energetique/énergies%20renouvelables/Panorama/';
if(id==2) window.opener.document.location='/content/situation%20energetique/bilans%20énergétiques/bilans%20départementaux%201999/';
if(id==1) window.opener.document.location='/content/Decouvrir%20energie/en%20savoir%20plus/';
window.close();
}
function awsprint() {
	printwindow=window.open('/frontoffice/print.html','printAWS','width=580,height=490,status=yes,scrollbars=yes');
	this.loadIsDone = false;
}
function awsprintnopopup() {
	if(bw.ns5) { // Correction BUG Moz
	// Impossible d'imprimer un long div positionné en absolu
	// Il es coupé.
		beforeprint()
		setTimeout("window.print(); afterprint()",1000)
	}
	else window.print();
	/*	if(window.onafterprint==undefined) { // Moz and others
			beforeprint()
			setTimeout("window.print(); afterprint()",1000)
		}
		else { // IE
			window.print();
		}*/
}
function fillPrintWindow(){
	
	htmlSource = opener.document.getElementById("toprint").innerHTML;
	printDiv = document.getElementById("printzone");
 	printDiv.innerHTML = htmlSource;
	return true;
}

function beforeprint() {
/*	var tab_div = document.getElementsByTagName("DIV");
	for(elt in tab_div) {
		if(tab_div[elt].id) {
			var block = ""+tab_div[elt].id
			if(block=="content") tab_div[elt].style.overflow="visible"
			if(block=="space") tab_div[elt].style.overflow="visible"
			if(block.substring(0,3)=="div") tab_div[elt].style.overflow="visible"
			
			if(block=="content") tab_div[elt].style.position="relative"
			if(block=="space") tab_div[elt].style.position="relative"
			if(block.substring(0,3)=="div") tab_div[elt].style.position="relative"

			if(block.substring(0,3)=="div") tab_div[elt].style.top="0px"
			if(block.substring(0,3)=="div") tab_div[elt].style.left="0px"
		}
	}*/
}
function afterprint() {
	/*	document.location = document.location.href*/
}

if(window.onbeforeprint!="undefined") window.onbeforeprint=beforeprint
if(window.onafterprint!="undefined") window.onafterprint=afterprint

function brique() {	
	if(eval(document.getElementById("content")) == null){
		return false; // pages non AWS - execution inutile
	}
	
	var tab_div = document.getElementsByTagName("DIV");
	var previousButt = -9999;
	var previousEnd = -9999;
	var previousLeft = -9999;
	var previousIsZE = 0;
	var previousHasMoved = 0;
	for(elt in tab_div) {
		if(tab_div[elt].id) {
			var block = ""+tab_div[elt].id
			
			//if(block=="content") tab_div[elt].style.overflow="visible"
			//if(block=="space") tab_div[elt].style.overflow="visible"
			//if(block=="content") tab_div[elt].style.position="relative"
			//if(block=="space") tab_div[elt].style.position="relative"
			
			if(block.substring(0,2)=="ze"){
				// rien
			}
			
			if(block.substring(0,2)=="bf"){
				
				if ((previousIsZE == 1) || (previousHasMoved == 1)){
					
					if (tab_div[elt].offsetTop < previousButt){
						if (tab_div[elt].parentNode.offsetLeft < previousEnd) {
							//alert("block" + tab_div[elt].id + ":\on depasse le previous end avec le coin gauche");
							myEnd = (tab_div[elt].offsetLeft+tab_div[elt].offsetWidth);
							if (myEnd > previousLeft) {
								//tab_div[elt].style.border="dotted";
								//alert("block" + tab_div[elt].id + ":\non depasse le previous left avec le coin droit : \nmyEnd = "+myEnd+" / previousLeft = "+previousLeft);
								tab_div[elt].style.top=String(tab_div[elt].offsetTop + previousButt-tab_div[elt].offsetTop)+"px";
								previousHasMoved = 1;
							}
						}
						//alert(block + " BF\n" + tab_div[elt].id + ".offsetTop = " + tab_div[elt].offsetTop + " previousButt = " + previousButt + "\n" + tab_div[elt].parentNode.id  + " must move by : " + (previousButt-(tab_div[elt].offsetTop)));						
					}
				}
				previousIsZE = 0;
			}
			
			if(block.substring(0,3)=="div"){
				tab_div[elt].style.overflow="auto";

				if ((previousIsZE == 1) || (previousHasMoved == 1)){
					
					if (tab_div[elt].parentNode.offsetTop < previousButt){
						
						if (tab_div[elt].parentNode.offsetLeft < previousEnd) {
							//alert("on depasse le previous end avec le coin gauche");
							myEnd = (tab_div[elt].parentNode.offsetLeft+tab_div[elt].parentNode.offsetWidth);
							if (myEnd > previousLeft) {
								//alert("block" + tab_div[elt].id + ":\non depasse le previous left avec le coin droit : \nmyEnd = "+myEnd+" / previousLeft = "+previousLeft);
								
								//alert(block + " ZE\n" + tab_div[elt].id + ".offsetTop = " + tab_div[elt].parentNode.offsetTop + " previousButt = " + previousButt + "\n" + tab_div[elt].parentNode.id  + " must move to : " + (tab_div[elt].parentNode.offsetTop+previousButt-(tab_div[elt].parentNode.offsetTop)));
								//alert(block + " ZE\n" + tab_div[elt].id + ".offsetLeft = " + tab_div[elt].parentNode.offsetLeft + " previousEnd = " + previousEnd + "\n" + tab_div[elt].parentNode.id  + " must move by : " + (previousEnd-(tab_div[elt].parentNode.offsetLeft)));
								tab_div[elt].parentNode.style.top = String(tab_div[elt].parentNode.offsetTop + (previousButt-tab_div[elt].parentNode.offsetTop))+"px";
								previousButt = tab_div[elt].parentNode.offsetTop + tab_div[elt].scrollHeight;
								//alert(previousButt);
								previousHasMoved =1;
							}
						}
					}
				}
				// previous n'est pas ZE
				if(tab_div[elt].scrollHeight > tab_div[elt].parentNode.scrollHeight){ // only si la brique a 'gonflé'	
					if ((tab_div[elt].parentNode.offsetTop + tab_div[elt].scrollHeight) > previousButt){ // si le butt est plus bas que le précédent
			 			previousButt = tab_div[elt].parentNode.offsetTop + tab_div[elt].scrollHeight;
						//alert(block + " ZE\n" + tab_div[elt].id + "scrollHeight = " + tab_div[elt].scrollHeight + " /  parentNode.scrollHeight = " + tab_div[elt].parentNode.scrollHeight + " \npreviousButt = " + previousButt);
						previousHasMoved =1;
					}
					else{
						// previousButt inchangé						
					}					
					previousEnd = tab_div[elt].parentNode.offsetLeft + tab_div[elt].scrollWidth;
					previousLeft = tab_div[elt].parentNode.offsetLeft;
					//alert(block + " ZE\n" + tab_div[elt].id + "scrollWidth = " + tab_div[elt].scrollWidth + " /  parentNode.scrollWidth = " + tab_div[elt].parentNode.scrollWidth + " \npreviousEnd = " + previousEnd);
				}
				else{
					//
				}
				tab_div[elt].style.overflow="visible";
				previousIsZE = 1;
			}
			
			if(block=="divhachures"){
				tab_div[elt].style.display="none";			
			}
		}		
	}
	
	if(previousButt < 0){
		//previousButt = 360;
	}
	// content est à l'intérieur de space
	if (eval(document.getElementById('content')) != null){ 
		if(previousButt > 0){
			document.getElementById('content').style.height = previousButt+"px";
		}
		else{
			//document.getElementById('content').style.height = "auto";
		}
		//document.getElementById('content').style.width = "810px";
		//document.getElementById('content').style.border = "dashed";
		document.getElementById('content').style["overflow-x"] = "visible";
		document.getElementById('content').style["overflow-y"] = "visible";
		document.getElementById('content').style["overflow"] = "visible";
		document.getElementById('content').style.overflow = "visible";
	}	
	
	// debut generique
	/*var footerBrique = 'bf6';//footer
	if (eval(document.getElementById(footerBrique)) != null) { 
		if (navigator.appVersion.indexOf("MSIE")>=0){
			document.getElementById(footerBrique).style["height"] = (getDivHeight(footerBrique)+8)+"px";
			//document.getElementById(footerBrique).style["border"] = "dotted";
		}
		else{
			document.getElementById(footerBrique).style.setProperty("height", (getDivHeight(footerBrique)+8)+"px", null);
			//document.getElementById(footerBrique).style.setProperty("border", "dotted", null);
		}
	
		if ((previousButt != undefined) && (previousButt > 0)){
			the_height = previousButt;
			the_height = the_height + 20;
			//alert(the_height + " butt methode");
			moveDivTo(footerBrique, eval(eval(removePX(the_height)))-0);
			if ((the_height < getWindowHeight()) && (the_height > 0)){
				// cas pages courtes
				the_height = getWindowHeight();
				the_height = the_height - getDivHeight(footerBrique) - 6;// 				
				//alert(the_height + " cas page courte - cas butt");				
			}
			else{
				//
			}
			the_height = the_height + "px";
			moveDivTo(footerBrique, eval(eval(removePX(the_height)))-0);	
		}
		else{
			the_height = getDivHeight('space');
			//alert(the_height + " space methode");			
			if ((the_height < document.getElementById(footerBrique).offsetTop) && (the_height > 0)){
				// cas pages courtes
				//the_height = document.getElementById(footerBrique).offsetTop;
				the_height = getWindowHeight();
				the_height = the_height - getDivHeight(footerBrique) - 6;// 
				the_height = the_height + "px";
				//alert(the_height + " cas page courte");					
			}	
			else if ((the_height < getWindowHeight()) && (the_height > 0)){
				// cas pages courtes
				the_height = getWindowHeight();
				the_height = the_height - getDivHeight(footerBrique) - 6;// 
				the_height = the_height + "px";
				//alert(the_height + " cas page courte - 2eme cas");
				moveDivTo(footerBrique, eval(eval(removePX(the_height)))-0);	
			}
			else if(removePX(the_height) == "0"){			
				the_height = getWindowHeight();
				the_height = the_height - getDivHeight(footerBrique) - 6;// 
				the_height = the_height + "px";	
				//alert(the_height + " cas null, window height");
				moveDivTo(footerBrique, eval(eval(removePX(the_height)))-0);	
			}	
			else{
				//alert(the_height + " cas ras (window is "+getWindowHeight()+")");
				//the_height + "px";
			}	
		}				
	}	*/
	return true;
}

function showprops(obj, obj_name) {
  for (var i in obj){
    document.write (obj_name+"."+i+"="+obj[i]+"<BR>");
  }
}
function alertprops(obj, obj_name) {
	tempStr = "";
  for (var i in obj){
   tempStr = tempStr + (obj_name+"."+i+"="+obj[i]+" - ");
   }
   alert(tempStr);
}

function getWindowHeight(wWin) {
	if (wWin == undefined){
		wWin = window;
	}
	var windowHeight = 0;
	if (typeof(eval(wWin).innerHeight) == 'number') {
		windowHeight = eval(wWin).innerHeight;		
	}
	else{	   
		if (eval(wWin).document.documentElement && eval(wWin).document.documentElement.clientHeight){
				windowHeight = eval(wWin).document.documentElement.clientHeight;
		}
		else if(eval(wWin).document.body && (eval(wWin).document.body.clientHeight > 1)){
        	windowHeight = eval(wWin).document.body.clientHeight;
        }	
	}
	return windowHeight;
}

function getWindowWidth(wWin) {
	if (wWin == undefined){
		wWin = window;
	}
	var windowWidth = 0;
   	if (typeof(eval(wWin).innerWidth) == 'number') {
        windowWidth = eval(wWin).innerWidth;
  	}
	else {
		if (eval(wWin).document.body && (eval(wWin).document.body.clientWidth > 1)) {
			windowWidth = eval(wWin).document.body.clientWidth;
		}
		else {
		  if (eval(wWin).document.documentElement && eval(wWin).document.documentElement.clientWidth) {
			windowWidth = eval(wWin).document.documentElement.clientWidth;
		  }
		}
   }
 return windowWidth;
}

function getDivHeight(id){
	if (eval(document.getElementById(id)) != null){	
		h = document.getElementById(id).clientHeight;
		for (i=0;i<=document.getElementById(id).childNodes.length;i++){
			if (eval(document.getElementById(id).childNodes[i]) != null) {
				tempH = document.getElementById(id).childNodes[i].clientHeight;
				if (tempH > h){
					h = tempH	
				}
			}
		}
		return h;
	}
	else{
		return 0;	
	}
}

function isDivOverOtherDiv(idA, idB){
	if ((eval(document.getElementById(idA)) != null)&&(eval(document.getElementById(idB)) != null)){ 
		basA = getDivHeight(idA) + document.getElementById(idA).offsetTop;
		hautB = document.getElementById(idB).offsetTop;
		//alert("basA = "+ basA+" - hautB = "+hautB);
		if (basA > hautB){
			return (basA - hautB);
		}
		else{
			return 0;
		}	
	}
	else{
		return 0;
	}
}

function moveDivTo(id, to){
	document.getElementById(id).style.top = to + "px";	
}

function moveDivBy(id, by){
	from = eval(document.getElementById(id).offsetTop);
	to =  eval(from + by);
	moveDivTo(id, to);
}

function removePX(str){
	str = String(str);
	pxPos = str.indexOf("px");
	if (pxPos > -1){
		str = str.substring(0, pxPos)+str.substring((pxPos+2));	
		return str;
	}
	else{
		return str;
	}
}

//OPEN FEVER PHOTOTEQUE
function openPictureWindow_Fever(imageType,imageName,imageWidth,imageHeight,alt,posLeft,posTop) {  // v4.01
	newWindow = window.open("","newWindow","width="+imageWidth+",height="+imageHeight+",scrollbars=no,left="+posLeft+",top="+posTop);

	newWindow.document.open();
	newWindow.document.write('<html><title>'+alt+'</title><body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginheight="0" marginwidth="0" onBlur="self.close()">'); 
	if (imageType == "swf"){
	newWindow.document.write('<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,1,0,0\" width=\"'+imageWidth+'\" height=\"'+imageHeight+'\">');
	newWindow.document.write('<param name=movie value=\"'+imageName+'\"><param name=quality value=high>');
	newWindow.document.write('<embed src=\"'+imageName+'\" quality=high pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"'+imageWidth+'\" height=\"'+imageHeight+'\">');
	newWindow.document.write('</embed></object>');	}else{
	newWindow.document.write('<img src=\"'+imageName+'\" width='+imageWidth+' height='+imageHeight+' alt=\"'+alt+'\">'); 	}
	newWindow.document.write('</body></html>');
	newWindow.document.close();
	newWindow.focus();
}

function go_n_close(url) {
			if (window.top.document.getElementById("awsiframe") != undefined){
				document.location.href = url;
			}
			else if (window.opener == null || window.opener == undefined){
				//newWin = window.open("/content/espace/inter.php?ifile="+url);
				newWin = window.open(url);
			}			 
			else{
				window.opener.document.location.href=url
				setTimeout("window.close()",100)
			}
		}

function go_n_close_ifpage(url) {
	var tab_ext=new Array('doc','pdf','xls','gif','jpg','jpeg','txt');
	var ispage = true;
	for(var i=0; i < tab_ext.length && ispage; i++) {
		if(url.indexOf(tab_ext[i])!=-1){
			ispage = false;
		}
	}	
	if(ispage) {
		if (window.opener.document.getElementById("awsiframe") != undefined){			
			window.opener.awsiframe.document.location=url;
		}
		else{
			window.opener.document.location=url;
		}
		setTimeout("window.close()",100);
	}
	else window.open(url,'','');
}

function selectUrl(sUrl){
	if ((sUrl != undefined)&&(sUrl != "")){
		openBrWindow(sUrl, "_blank", getWindowWidth(window.top), getWindowHeight(window.top), "toolbar=yes,scrollbars=yes, location=yes,menubar=yes,directories=yes");
	}	
}

function ilink(sUrl, sTarget){
	sTarget = String(sTarget);
	sUrl = String(sUrl);
	if (window[sTarget] != undefined){
	//	if (String(document.getElementById(target)) == "[object HTMLObjectElement]"){
		window[sTarget].location.replace(sUrl); // MOZ
	}
	else{
		this.location.replace(sUrl); // MOZ
	}
	//}
	//else{ // IE
	//	oTag = document.getElementById(String(target));		
	//	oTag.parentNode.innerHTML = '<object width="970" height="100%" type="text/html" codetype="text/html" data="'+url+'"  id="awsiframe" name="awsiframe" classid="clsid:25336920-03F9-11CF-8FD0-00AA00686F13">';
	//}
}

function ilink_parent(sUrl, sTarget){
	sTarget = String(sTarget)
	sUrl = String(sUrl);
	//	if (String(document.getElementById(target)) == "[object HTMLObjectElement]"){
	window.parent.location.replace(sUrl); // MOZ
	//}
	//else{ // IE
	//	oTag = document.getElementById(String(target));		
	//	oTag.parentNode.innerHTML = '<object width="970" height="100%" type="text/html" codetype="text/html" data="'+url+'"  id="awsiframe" name="awsiframe" classid="clsid:25336920-03F9-11CF-8FD0-00AA00686F13">';
	//}
}

function resizeSWF(id, w, h){
	//alert(w+" / "+h);
	if (document.getElementById(id) != undefined){
		if (w != undefined){
			document.getElementById(id).width = w;	
		}
		if (h != undefined){
			document.getElementById(id).height = h;	
		}
	}
	/*	a = document.getElementsByTagName("embed");
	for (i in a){
		if (a[i].src != undefined){
			if (a[i].id == id){ // SID ************
				if (w != undefined){
					a[i].width = w;
				}
				if (h != undefined){
					a[i].height = h;
				}
			}			
		}
	}*/	
	a = document.getElementsByTagName("object");	
	for (i in a){
		if (a[i].id != undefined){
			if (a[i].id == id){
				if (w != undefined){
					a[i].width = w;
				}
				if (h != undefined){
					a[i].height = h;
				}					
			}	
		}
	}
}

function getRedirURL(baseURL, currentAccesPath){	
	  // pré requis compatiblité URL	  
	  // & >> %26
	  currentAccesPath = currentAccesPath.replace("&","%26");
	  // ? >> &
	  currentAccesPath = currentAccesPath.replace("?","&");	  
	  
	  return baseURL+currentAccesPath;	  
}

function base64_encode(text) {
  var dwOctets = 0;
  var nbChars = 0;
  var ret = "";
  var b;

  for (i = 0; i < 3 * ((text.length + 2) / 3); i++) {
    if (i < text.length) b = text.charCodeAt(i);
    else b = 0;
    dwOctets <<= 8;
    dwOctets += b;
    if (++nbChars == 3) {
      for (j = 0; j < 4; j++) {
        b = (dwOctets & 0x00FC0000) >> 18;
        if (b < 26) ret += String.fromCharCode(b + 65);
        else if (b < 52) ret += String.fromCharCode(b + 71);
        else if (b < 62) ret += String.fromCharCode(b - 4);
        else if (b == 62) ret += "+";
        else if (b == 63) ret += "/";
        dwOctets <<= 6;
      }
      dwOctets = 0;
      nbChars = 0;
    }
  }

  ret += "=";

  return ret;
}

function base64_decode(text) {
  var dwOctets = 0;
  var nbChars = 0;
  var ret = "";
  var b;

  for (i = 0; i < text.length; i++) {
    b = text.charCodeAt(i);
    if (b == 61) break;
    if (b > 32) {
      dwOctets <<= 6;
      if (65 <= b && b <= 90) dwOctets += b - 65;
      else if (97 <= b && b <= 122) dwOctets += b - 71;
      else if (48 <= b && b <= 57) dwOctets += b + 4;
      else if (b == 43) dwOctets += 62;
      else if (b == 47) dwOctets += 63;
      if (++nbChars == 4) {
        for (j = 0; j < 3; j++) {
          ret += String.fromCharCode((dwOctets & 0x00FF0000) >> 16);
          dwOctets <<= 8;
        }
        dwOctets = 0;
        nbChars = 0;
      }
    }
  }

  return ret;
}


function jsonIE7Stringify(jsonData) {
	var strJsonData = '{';
	var itemCount = 0;
	for (var item in jsonData) {
		if (itemCount > 0)
			strJsonData += ', ';
		temp = jsonData[item];
		if (typeof(temp) == 'object')
			s =  Stringify(temp);    
		else	s = '"' + temp + '"';  
		strJsonData += '"' + item + '":' + s;
		itemCount++;
	}
	strJsonData += '}';
	return strJsonData;
}

// Generic clear form function

function genericClearForm (_form) {	
	if ($.browser.msie){	
		var versionIE = parseInt($.browser.version);
		if(versionIE < 10) {
			$('#'+_form+' :input').not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
			$('#'+_form+' select').each(function(index) {
				$("option[value='-1']", this).attr('selected','selected');
				$("option[value='']", this).attr('selected','selected');
			});			
		}
	} else {
		$('#'+_form+' :input').not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
	}	
}




/*
	Standard form fields checking library
*/

// based on JQuery element handling.
// form fields parameters are recognized as JQuery entities
// return form field to callee with related error code (when an error occurs)

/*************** ACCOUNTS ****************/

function formCheckValidEmail (_field, _func_OK, _func_KO){	
	//var filter=/^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
	//var filter=/^([a-zA-Z0-9]{3,})(((\.|\-|\_)[a-zA-Z0-9]{2,})+)?@([a-z]{3,})(\-[a-z0-9]{3,})?(\.[a-z]{2,})+$/;
	var filter=/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;
	if (filter.test(_field.val())) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_email');
		return false;
	}
}

function formCheckConfirmEquals (_field, _ref, _func_OK, _func_KO) {
	if (_field.val() != _ref.val()) {
		_func_KO.call(_field, _field, 'confirm_differs');
		return false;
	} else {
		_func_OK.call(_field, _field);
		return true;
	}
}

/*************** NUMBERS AND COMPARISONS ****************/

// USAGE : formCheckValidValue(	$('#my_field'), [0,1,2,3,4], function(){}, function(){}	);
function formCheckValidValue (_field, _ref, _func_OK, _func_KO){	
	if (_ref.in_array(_field.val())) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_value');
		return false;
	}
}

function formCheckValidInteger (_field, _func_OK, _func_KO){	
	var filter=/^(\+|-)?[0-9]+$/;
	if (filter.test(_field.val())) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_integer');
		return false;
	}
}

// Also accepts spaces (ex phone numbers
function formCheckValidIntegerPlus (_field, _func_OK, _func_KO){	
	var filter=/^(\+|-)?[0-9\s]+$/;
	if (filter.test(_field.val())) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_value');
		return false;
	}
}

function formCheckValidNumber (_field, _func_OK, _func_KO){	
	var filter=/^\s*\d+\s*$/;
	if (filter.test(_field.val())) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_number');
		return false;
	}
}

function formCheckValidDecimal (_field, _func_OK, _func_KO){	
	var filter=/^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/;
	if (filter.test(_field.val())) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_decimal');
		return false;
	}
}

function formCheckLowerNumber (_field, _ref, _func_OK, _func_KO){	
	if (_field.val() < _ref) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'not_lower_value');
		return false;
	}
}

function formCheckLowerEqualNumber (_field, _ref, _func_OK, _func_KO){	
	if (_field.val() <= _ref) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'not_lower_equal');
		return false;
	}
}

function formCheckHigherNumber (_field, _ref, _func_OK, _func_KO){	
	if (_field.val() > _ref) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'not_higher_value');
		return false;
	}
}

function formCheckHigherEqualNumber (_field, _ref, _func_OK, _func_KO){	
	if (_field.val() >= _ref) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'not_higher_equal');
		return false;
	}
}

function formCheckNotEmptyCheckbox (_field, _func_OK, _func_KO) {
	 
	if (_field.is(':checked') ) { 
		_func_OK.call(_field, _field);
		return true;
	} else { 
		_func_KO.call(_field, _field, 'empty_value');
		return false;
	}
}
/*************** CREDIT CARDS ****************/

// Check for valid credit card type/number
function formCheckValidCreditCard (_field, _type, _func_OK, _func_KO) {
	var creditCardList = [
		//type      prefix   length
		["amex",    "34",    15],
		["amex",    "37",    15],
		["disc",    "6011",  16],
		["mc",      "51",    16],
		["mc",      "52",    16],
		["mc",      "53",    16],
		["mc",      "54",    16],
		["mc",      "55",    16],
		["visa",    "4",     13],
		["visa",    "4",     16] ];
	var cc = getdigits(ccnumber);
	if (luhn(cc)) {
		for (var i in creditCardList) {
			if (creditCardList[i][0] == _type.toLowerCase()) {
				if (cc.indexOf(creditCardList [i][1]) == 0) {
					if (creditCardList [i][2] == cc.length) {
						_func_KO.call(_field, _field, 'invalid_ccard');
						return true;
					}
				}
			}
		}
	}
	_func_OK.call(_field, _field);
	return false;
}

function luhn (cc) {
	var sum = 0;
	for (var i = cc.length - 2; i >= 0; i -= 2)
		sum += Array (0, 2, 4, 6, 8, 1, 3, 5, 7, 9) [parseInt(cc.charAt(i), 10)];
	for (i = cc.length - 1; i >= 0; i -= 2)
		sum += parseInt(cc.charAt(i), 10);
	return (sum % 10) == 0;
}

/*************** MISCELLANEOUS ****************/

function formCheckNotEmpty (_field, _func_OK, _func_KO) {
	if (_field.val() != '') {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'empty_value');
		return false;
	}
}

function formNotEmptySelect (_field, _index, _func_OK, _func_KO) {
	if (_index.val() != -1) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'empty_value');
		return false;
	}
}


function formCheckNotNumeric (_field, _func_OK, _func_KO) {
	var filter=/^\D{1,60}$/;
	if (filter.test(_field.val())) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'not_alpha_value');
		return false;
	}
}

function formCheckValidPhone (_field, _spaces, _func_OK, _func_KO) {
	if (_spaces)
		var filter=/^(\+|-)?[0-9]+ ?$/;
	else	var filter=/^(\+|-)?[0-9]+$/;
	if (filter.test(_field.val()) && _field.val().length < 15) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_phone');
		return false;
	}
}

function formCheckValidPhone_nofunction (_field, _spaces) {
	if (_spaces)
		var filter=/^(\+|-)?[0-9]+ ?$/;
	else	var filter=/^(\+|-)?[0-9]+$/;
	if (filter.test(_field.val())) { 
		return true;
	} else { 
		return false;
	}
}

function formCheckCharSize (_field, _len, _func_OK, _func_KO) {
	if (_field.val().length == _len) {
		_func_OK.call(_field, _field);
		return true;
	} else {
		_func_KO.call(_field, _field, 'invalid_length');
		return false;
	}
}
 
function formCheckCharSize_nofunction (_field, _len ) {
	if (_field.val().length == _len) { 
		return true;
	} else { 
		return false;
	}
}

/*************** HELPERS ****************/

//If not declared anywhere else, uncomment this one

Array.prototype.in_array = function(p_val) {
	for (var i = 0; i < this.length; i++) {
		if (this[i] == p_val)
			return true;
	}
	return false;
}





// helper
function getCheckboxes(_index) { 
	/*return $(_index).find(':checkbox').map(function(i,n) {
		return $(n);
	}).get();*/
	
	 return(
		   
		   $("input[name="+_index+"]:checked").map(
     function () {return this.value;}).get());

	
	
}

// clic sur la case cocher/decocher  
function toggleCheckboxes(_box, _index) {
	// on cherche les checkbox qui dépendent de la liste 'cases'
	var cases = $("#cases_"+_index).find(':checkbox');
	cases.attr('checked', _box.checked);
}

// vérifie la sélection du checbox "select/deselect all"
function verifyCheckAll(_index) { 
	var status = verifyCheckboxesStatus(_index);
	$('#cocheTout_'+_index).attr('checked', status);
}


function verifyCheckAllCheckbox(_field, _index, _func_OK, _func_KO) {

	var status = verifyCheckboxesStatus2(_index);
	 
	if (status) { 
		_func_OK.call(_field, _field);
		return true;
	} else { 
		_func_KO.call(_field, _field, 'empty_value');
		return false;
	}  
}


function verifyCheckboxesStatus(_index) {
	// on cherche les checkbox qui dépendent de la liste 'cases'
	var cases = getCheckboxes(_index);
	var ischecked = false;
	for (var i=0; i<cases.length; i++) {
		if (cases[i].attr('checked'))
			ischecked = true;
	}
	return ischecked;
}

function verifyCheckboxesStatus2(_index) {
	// on cherche les checkbox qui dépendent de la liste 'cases'
	 

	var cases = getCheckboxes(_index);
	var ischecked = false; 
	if (cases.length > 0)  ischecked = true;
	return ischecked;
}

//******************* pattern ************//

function formCheckPattern (_field, pattern, _func_OK, _func_KO) {
	
	/*
	
	var eCountPattern = 0; 
	var sMessagePattern; 
	//var filter=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;   
 //var re = new RegExp( '^\d{1,' + limit + '}\,\d{2}$' );[/color]
		
   
	//var filter = new RegExp(eval('/^'+pattern+'$/')); 
	var filter = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/"; 
	alert(filter+" "+_field.val());
	if (!filter.text(_field.val())){
		eCountPattern++;
		sMessagePattern+= "Le champs "+_field+" ne doit contenir que  les expressions suivantes: "+pattern;
	} 
	if (eCountPattern == 0) {
	alert('ok'); 
	}
	else{
	alert(sMessagePattern); 
	}
	*/
	/*if (_field.is(':checked') ) { 
		_func_OK.call(_field, _field);
		return true;
	} else { 
		_func_KO.call(_field, _field, 'empty_value');
		return false;
	}*/
}


function trim (myString)
{ 
	
	if (myString != '' && myString != 'undefined' && myString != null) {
		return myString.replace(/^\s+/g,'').replace(/\s+$/g,'');
	}
	else {
		return myString;
	}
	  
} 