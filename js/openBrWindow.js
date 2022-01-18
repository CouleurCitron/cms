// Ouvre une fenêtre de navigateur centrée ou non.

function openBrWindow(theURL,winName,width,height,features,bCentre) { //v3.0 by Kaori Dév
  var window_width = width;
	var window_height = height; 
	var newfeatures= "," + features;
	var window_top = (screen.height-window_height)/2;
	var window_left = (screen.width-window_width)/2;
	if(bCentre == 'false'){
		newWindow=window.open(''+ theURL + '',''+ winName + '','width=' + window_width + ',height=' + window_height + newfeatures + '');
	}else{  

		url = theURL;
		nomFenetre = winName;
		optionsFenetre = 'width=' + window_width + ',height=' + window_height + ',top=' + window_top + ',left=' + window_left + newfeatures;

		newWindow=window.open(''+ url + '', '' + nomFenetre + '', '' + optionsFenetre + '');
	}
	
  newWindow.focus();
}   

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function openWYSYWYGWindow(theURL,winName,width,height,features,bCentre,idField,idForm, Type) {
	
  /**
  width et height sont deprecated  
  */
  
  
  var htmlcode = document.forms[idForm].elements[idField].value;
  
  if (theURL.indexOf("?") < 0 ) theURL = theURL + "?vars";
  theURL = theURL + "&idField="+idField+"&idForm="+idForm+"&Type="+Type+"&";
  
  var window_width = Math.round(screen.width*.8);
  var window_height = Math.round(screen.height*.8);
  if (window_width>800){
 	window_width = 800; 
	}
  theURL +="height="+window_height+"&";
  
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

function openLinkWindow(theURL,winName,width,height,features,bCentre,idField,idForm) {
  var htmlcode = document.forms[idForm].elements[idField].value;
  if (theURL.indexOf("?") < 0 ) theURL = theURL + "?";
  else theURL = theURL + "&";
  theURL = theURL + "idField="+idField+"&idForm="+idForm+"&source="+escape(htmlcode)+"&";
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

//function openMapsWindow(theURL,winName,width,height,features,bCentre,idField,idForm) {
function openMapsWindow(theURL,winName,width,height,features,bCentre,tableName,idField) {
//  var htmlcode = document.forms[idForm].elements[idField].value;
//  theURL = theURL + "?idField="+idField+"&idForm="+idForm+"&source="+escape(htmlcode)+"&";
  theURL = theURL + "?tableName="+tableName+"&idField="+idField+"&";
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