var largeurFrame, largerEcran, millieuFrame, millieuEcran, xZero;

// Déclaration du tableau
function TOF_Calk(iLeftX, iRightX, iHeight, sNom, sImgName, sImgRestore){
	this.iLeftX = iLeftX;
	this.iRightX = iRightX;
	this.iHeight = iHeight;
	this.sNom = sNom;
	this.sImgName = sImgName;
	this.sImgRestore = sImgRestore;
}

Tb_Calk = new Array();

//Tb_Calk[Indice du tableau] = new TOF_Calk(iLeftX, iRightX, sNom, bEtat);
// iLeftX : Position gauche du calque par rapport au bord gauche de la page
// iRightX : iLeft + la largeur du calque
// iHeight : Hauteur du calque
// sNom : nom du calque

Tb_Calk[0] = new TOF_Calk(870,950,380, 'divContainerPubli', 'publi', '/img/fo/gabarits/hp/publications.gif');


if (document.all)    {n=0;ie=1;n6=0;}
if (document.layers) {n=1;ie=0;n6=0;}
if(!document.all && document.getElementById) {n=0;ie=0;n6=1};

function updateIt(e){
	if (ie){
		var x = window.event.clientX;
		var y = window.event.clientY;
		TOF_HideAll(x,y);
	}
	if (n){
		var x = e.pageX;
		var y = e.pageY;
		TOF_HideAll(x,y)
	}
	if (n6){
		var x = e.pageX;
		var y = e.pageY;
		TOF_HideAll(x,y)
	}
	//alert("x : " + x + " y : " + y + " x-xZero : " + (x-xZero))
}

function TOF_HideAll(x,y){
	for(ii=0;ii<=(Tb_Calk.length-1);ii++){
		objSel = document.getElementById(Tb_Calk[ii].sNom);
		objImg = document.getElementById(Tb_Calk[ii].sImgName);
		
		if ((x-xZero) > Tb_Calk[ii].iRightX || (x-xZero) < Tb_Calk[ii].iLeftX || y > (Tb_Calk[ii].iHeight) || y < 246){
			objSel.style.setAttribute("visibility","hidden");
			objImg.src = Tb_Calk[ii].sImgRestore;
		}
	}
}

function TOF_Hide(){
	for(ii=0;ii<=(Tb_Calk.length-1);ii++){
		objSel = document.getElementById(Tb_Calk[ii].sNom);
		objSel.style.setAttribute("visibility","hidden");
	}
}


function TOF_Show(sCalk){
	objSel = document.getElementById(sCalk);
	objSel.style.setAttribute("visibility","visible");
}


function ChangeData(){
	largeurEcran = document.body.clientWidth;

	millieuFrame = largeurFrame/2;
	millieuEcran = largeurEcran/2;

	xZero = millieuEcran - millieuFrame;
}

function f_InitBody(){
	
	largeurFrame = 949;
	largeurEcran = document.body.clientWidth;

	millieuFrame = largeurFrame/2;
	millieuEcran = largeurEcran/2;

	xZero = millieuEcran - millieuFrame;

	if (document.all){
		document.body.onClick=TOF_Hide();
		document.body.onScroll=TOF_Hide();
		document.body.onmousemove=updateIt;
		document.body.onresize=ChangeData;
	}
	if (document.layers){
		document.onmousedown=TOF_Hide();
		window.captureEvents(Event.MOUSEMOVE);
		window.onmousemove=updateIt;
		window.onresize=ChangeData;
	}
	if(!document.all && document.getElementById){
		document.onmousedown=TOF_Hide();
		window.captureEvents(Event.MOUSEMOVE);
		window.onmousemove=updateIt;
		window.onresize=ChangeData;
	}
}