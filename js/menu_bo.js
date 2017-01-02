/*	Mkl : attention si problème avec le menu, voir plus loin dans le code
	updateLink(bOpen);
	Enlevé pour une seule raison, ca fait  déconner les liens sous IE
	les %E9 pour les liens avec accent sont mal interprêté
	je n'ai pas pousser plus loin l'investigation, mais en activant cette fonction, ca marche plus!
	et elle semble pas indispensable (le menu fonctionne sans)...
	*/



// Déclaration du tableau

var debug = 0;

function TOF_Calk(iTop, iHeight, iPosClose, iPosOpen, sNom){
  this.iTop = iTop
  this.iHeight = iHeight;
  this.iPosClose = iPosClose;
  this.iPosOpen = iPosOpen;
  this.sNom = sNom;
}

Tb_Calk = new Array();

Tb_Calk[0] = new TOF_Calk(0, 600, 0, 0, 'nav');

if(document.all)    {n=0;ie=1;n6=0;}
if(document.layers) {n=1;ie=0;n6=0;}
if(!document.all && document.getElementById) {n=0;ie=0;n6=1}

function updateIt(ev){
  var x=0;
  var y=0;
  if(n6){
    x = ev.pageX;
    y = ev.pageY;
  } else {
    x = event.x;
    y = event.y;
  }
  TOF_MoveAll(x,y);
}

function OpenMenu(strCalkName){
	objLayer = document.getElementById(strCalkName);
	if(bOpen == false){
		objLayer.style.left = "0px";
		bOpen = true;
	}else{
		objLayer.style.left = "-240px";
		bOpen = false;
	}
	updateLink(bOpen);
}

/*function EscamotLayer(strCalkName){
  objLayer = document.getElementById(strCalkName);
	if(bOpen == true){
		objLayer.style.left = "0px";
	}else{
		objLayer.style.left = "-240px";
	}
	// updateLink(bOpen);
	//Enlevé pour une seule raison, ca fait  déconner les liens sous IE
	//les %E9 pour les liens avec accent sont mal interprêté
	//je n'ai pas pousser plus loin l'investigation, mais en activant cette fonction, ca marche plus!
	//et elle semble pas indispensable (le menu fonctionne sans)...
	
}*/

function updateLink(menuOpen){
	iNbLink = document.links.length;
	if (iNbLink < 100) {
	for(iOO=0;iOO<iNbLink;iOO++){
		sLien = document.links[iOO].href;
		if(sLien.indexOf("javascript") == -1){
			if(sLien.indexOf("#")==-1) {
				if(sLien.indexOf("?") == -1){
					document.links[iOO].href = sLien + "?menuOpen="+ menuOpen;
				}else{
					document.links[iOO].href = makeLink(sLien) + "menuOpen=" + menuOpen;
				}
			} else {
				//tAnchor = sLien.split('#');
				//sLien = tAnchor[0];
				//if(sLien.indexOf("?") == -1){
				//	document.links[iOO].href = sLien + "?menuOpen="+ menuOpen;
				//}else{
				//	document.links[iOO].href = makeLink(sLien) + "menuOpen=" + menuOpen;
				//}
				//document.links[iOO].href = document.links[iOO].href + '#' + tAnchor[1];
			}
		}
	}
	}
	updateForm(menuOpen);
}

function updateForm(menuOpen){
	iNbForm = document.forms.length;
	for(iOO=0;iOO<iNbForm;iOO++){
    sLien = document.forms[iOO].action;
    debug = 1;
    if(sLien.indexOf("?") == -1){
      document.forms[iOO].action = sLien + "?menuOpen="+ menuOpen;
    }else{
      document.forms[iOO].action = makeLink(sLien) + "menuOpen=" + menuOpen;
    }
    debug = 0;
  }
}

function makeLink(sLink){
	if(sLien.indexOf("#")!=-1)
		return sLien;
	tbAdresse = sLink.split("?");
	sUrl = tbAdresse[0];
		
	//sProfil = /=|&\W*/;
	//sProfil = /=|&/;
	tbQuery = tbAdresse[1].split('&');
	tbResult = new Array();
	for(i=0;i<tbQuery.length;i++) {
		tbTmp = tbQuery[i].split('=');
		tbResult[2*i] = tbTmp[0];
		tbResult[2*i+1] = tbTmp[1];
	}
	
	sNewLink = sUrl + "?"
	
	for(iYY=0;iYY<tbResult.length;iYY+=2){
		if(tbResult[iYY] != 'menuOpen'){
			if(typeof(tbResult[iYY+1])!="undefined")
				sNewLink += tbResult[iYY]+"="+tbResult[iYY+1]+"&";
			else
				sNewLink += tbResult[iYY]+"=&";
		}
	}
	if(sNewLink.match(/=$/)) {
		sNewLink+='&';
	}
	return sNewLink;
}

function TOF_MoveAll(iPosX, iPosY){
  objLayer = document.getElementById(Tb_Calk[0].sNom);

  if(iPosX > (Tb_Calk[0].iPosOpen+260) || iPosY > Tb_Calk[0].iHeight){
    objLayer.style.left = Tb_Calk[0].iPosClose+"px";
  }
  
  if(iPosX <= 10 && iPosY <= Tb_Calk[0].iHeight){
    objLayer.style.left = Tb_Calk[0].iPosOpen+"px";
  }
}

function TOF_MoveLayer(){
  updateIt();
}

function f_InitBody(){
  if(ie){
    // gestion des évènements pour IE
    document.body.onClick=updateIt();
    document.body.onScroll=updateIt();
    document.onmousemove=updateIt;
  }
  if(n){
    // gestion des évènements pour NN
    window.captureEvents(Event.MOUSEMOVE | Event.MOUSEDOWN);
    window.onmousedown=updateIt;
    window.onmousemove=updateIt;
  }
  if(n6){
    // gestion des évènements pour N6
    window.captureEvents(Event.MOUSEMOVE | Event.MOUSEDOWN);
    window.onmousedown=updateIt;
    window.onmousemove=updateIt;
  }
}

function toggleTous(qui){
	inputs = document.getElementsByTagName('input');
	for (i=0;i<inputs.length;i++){
		if ((inputs[i].type=='checkbox')	&& (inputs[i].id.indexOf(qui.id)==0)){
			inputs[i].checked=qui.checked;			
		}	
	}
}