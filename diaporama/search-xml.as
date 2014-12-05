_parent.mask._width = 750;
_parent.mask.duplicateMovieClip("overMask", 6000);
_parent.mask._visible = false;

_parent.entry._visible = false;
_parent.entry_sub._visible = false;

// offset Y des elements de la liste
yOffset = 60;
yOffsetLignetexteSimple = 13;
yOffsetLignetexteDouble = 30;
yOffsetSeparateur = 15;
xOffset = 125;

nbreMaxEntryToList = 4;

function getMyName(command){
	if (command == "path"){
		myName = this._url.substring(0,this._url.lastIndexOf("."));		 
	}
	else if (command == "parent"){
		myName = this._url.substring(0,this._url.lastIndexOf("/"));		 
	}
	else{
		myName = this._url.substring(this._url.lastIndexOf("/")+1,this._url.lastIndexOf("."));
	}
	//trace("my name is "+myName);
	return myName;
}

//XML
if (nodeCount == undefined){
	nodeCount=new Object(); //objet qui contiendra tous les compteurs qui servent a parser le xml, que l'on virera après
	rawData = new XML(); //objet ki contient les données XML
	rawData.ignoreWhite = false; //prend en compte les espace blanc (au cas ou y a juste un espace ki sépare deux zones de textes);
}

//***FONCTIONS***

function resetValues(){
	_global.toucher = 0;
	_global.gout = 0;
	_global.vue = 0;
	_global.ouie = 0;
	_global.odorat = 0;
	_global.rechtype = "";
	_global.rechpublic = "";
	_global.rechlieu = "";
	
	_global.dateStr = "";
	
	_global.enablepublic = false;
	_global.enablelieu = false;
	_global.enabletype = false;
}

function frenchdate2integer(dateStr){
	dateStr = dateStr.toLowerCase();
	
	// jours de la semaine
	if (dateStr.indexOf("lundi ") > -1){
		dateStr = dateStr.substr(6);
	}
	else if (dateStr.indexOf("mardi ") > -1){
		dateStr = dateStr.substr(6);
	}
	else if (dateStr.indexOf("mercredi ") > -1){
		dateStr = dateStr.substr(9);
	}
	else if (dateStr.indexOf("jeudi ") > -1){
		dateStr = dateStr.substr(6);
	}
	else if (dateStr.indexOf("vendredi ") > -1){
		dateStr = dateStr.substr(9);
	}
	else if (dateStr.indexOf("samedi ") > -1){
		dateStr = dateStr.substr(7);
	}
	else if (dateStr.indexOf("dimanche ") > -1){
		dateStr = dateStr.substr(9);
	}
	// mois ---------------
	if (dateStr.indexOf("octobre") > -1){
		mois ="10";
		jour = dateStr.substr(0,dateStr.indexOf("octobre")-1);
	}
	else if (dateStr.indexOf("novembre") > -1){
		mois ="11";
		jour = dateStr.substr(0,dateStr.indexOf("novembre")-1);
	}
	else if (dateStr.indexOf("décembre") > -1){
		mois ="12";
		jour = dateStr.substr(0,dateStr.indexOf("décembre")-1);
	}

	return mois+jour;
}

function buildMenu(){ 
	
	if ((nbreMaxEntryToList+1) >_global.tabgout.length){
		borneBoucleFor = _global.tabgout.length;
	}
	else{
		borneBoucleFor = nbreMaxEntryToList+1;
	}
	
	for(iEnr=0;iEnr<borneBoucleFor;iEnr++){ // on bride à nbreMaxEntryToList entrées maxi

		// début du proc de présentation des res

		// duplication et placement
		/*
		entryName = "entry_"+(iEnr);
		_parent.entry._visible = false;
		_parent.entry.duplicateMovieClip(entryName, 5000+iEnr);
		_parent[entryName]._visible = false;
		extraLines = 0;
		*/
		
		// gestion des textes
		if ((_global.tabtext[iEnr] != undefined) and (_global.tabtext[iEnr] != "")){
			_parent.diapo["texte"+iEnr].text = _global.tabtext[iEnr];
		}
		else{
			_parent.diapo["texte"+iEnr].text = "";
		}
		_parent.diapo["texte"+iEnr]._visible = false;

		
		// photo
		if ((_global.tabjpg[iEnr] != undefined) and (_global.tabjpg[iEnr] != "")){
			_parent["photo"+iEnr].loadMovie(getMyName("parent")+"/"+_global.tabjpg[iEnr]);
			_parent.diapo["photo"+iEnr].loadMovie(getMyName("parent")+"/"+_global.tabjpg[iEnr]);
			// gestion des boutons
			_parent.diapo["bout"+iEnr].useHandCursor = true;
		}
		else{
			_parent.diapo["photo"+iEnr]._visible = false;
			// gestion des boutons
			_parent.diapo["bout"+iEnr].useHandCursor = false;
			_parent.diapo.thumbs["gris"+iEnr]._visible = false;
		}
		_parent["photo"+iEnr]._visible = false;
		
		//_parent[entryName]._visible = true;
		
		// on apparait de g a d
		//_parent.overMask.smoothMove(undefined, undefined, 750, undefined, 50, 8);
	}
	_parent.diapo.bout0.useHandCursor = true;
	_parent.diapo.texte0._visible = true;
	_parent.diapo.photo0._visible = true;

}

//-=FONCTIONS XML=-

//cette fonction parcourt le noeud spécifié, fait les opérations nécéssaires
//pour construire l'exercice par rapport a ce qu'elle trouve, et explore tout ses gosses par récursivité
//Param( node XML a explorer)
function exploreNode(curNode){
	//selon le noeud qu'on a fait les opération nécéssaires
	//si c'est un noeud d'arbo
	if( curNode.nodeType==1 ){
		//si c'est le noeud item		
		if( curNode.nodeName.toLowerCase() == "fiche" ){
			//récupère les attributs
			for (attr in curNode.attributes){ // on parcourt les attributs du noeud
				if (_global["tab"+attr] == undefined){ // on cree la matrice si elle n'existe pas
					_global["tab"+attr] = new Array(String(curNode.attributes[attr]));
				}	
				else{ // si la matrice existe, on l'alimente
					_global["tab"+attr].push(String(curNode.attributes[attr]))
				}
			}
			//quite la fonction
			return true;
		}
	}
	//puis explore les enfants par recursivité
	for (nodeCount[curNode+"_count"]=0; nodeCount[curNode+"_count"]<curNode.childNodes.length; nodeCount[curNode+"_count"]++) {
		//explore l'enfant
		exploreNode(curNode.childNodes[nodeCount[curNode+"_count"]]);
	}
}

//-=FIN FONCTIONS XML=-

// fonction animation

//Utilisation  : donner les dimension de depart et de fin. Plus la vitesse est petite, plus le déplacement est rapide
//si on ne rentre rien pour les 4 premiers parametres, les valeurs seront celles du clip
//Resultat : déplace un clip progressivement : vite au début, ralenti vers la fin

MovieClip.prototype.smoothScale= function (xDepart, yDepart, xDest, yDest, vitesse, profondeur) {
	if (profondeur == undefined) var profondeur = 1000;
	if (xDepart == undefined) var xDepart = this._width;
	if (yDepart == undefined) var yDepart = this._height;
	if (xDest == undefined) var xDest = this._width;
	if (yDest == undefined) var yDest = this._height;
	this.createEmptyMovieClip("mcSmoothScale", profondeur);
	this.mcSmoothScale.onEnterFrame = function() {
		if (Math.abs(Math.abs(this._parent._width)-Math.abs(xDest)) > 1 || Math.abs(Math.abs(this._parent._height)-Math.abs(yDest)) > 1) {
			this._parent._width += (xDest-this._parent_width)/vitesse;
			this._parent._height += (yDest-this._parent._height)/vitesse;
		}
		else {
			this._parent._width = xDest;
			this._parent._height = yDest;
			this.onEnterFrame = null;
		}
	}
	return true;
}

MovieClip.prototype.smoothMove = function (xDepart, yDepart, xDest, yDest, vitesse, profondeur) {
		if (profondeur == undefined) var profondeur = 1000;
	if (xDepart == undefined) var xDepart = this._x;
	if (yDepart == undefined) var yDepart = this._y;
	if (xDest == undefined) var xDest = this._x;
	if (yDest == undefined) var yDest = this._y;
	this.createEmptyMovieClip("mcSmoothMove", profondeur);
	this.mcSmoothMove.onEnterFrame = function() {
		if (Math.abs(Math.abs(this._parent._x)-Math.abs(xDest)) > 1 || Math.abs(Math.abs(this._parent._y)-Math.abs(yDest)) > 1) {
			this._parent._x += (xDest-this._parent._x)/vitesse;
			this._parent._y += (yDest-this._parent._y)/vitesse;
		}
		else {
			this._parent._x = xDest;
			this._parent._y = yDest;
			this.onEnterFrame = null;
		}
	}
	//trace("--------- fin du smoothMove");
	return true;
}
//------------------------------------------------------

function splitChaine(letheme){
	mid = Math.floor(letheme.length / 2)+1;
	bestDistance = mid;
	bestSpace = letheme.length;
	for (iSplit=0; iSplit<letheme.length;iSplit++){
		if (letheme.charAt(iSplit) == " "){
			distance = Math.abs(iSplit - mid);
			if (distance < bestDistance){
				bestDistance = distance;
				bestSpace = iSplit;
			}
		}
	}
	return letheme.substr(0, bestSpace) + "\n" + letheme.substr(bestSpace+1);
}
//////////////////////////////

function splitChaineCesure(chaine, cesurepos, quelmid){
	// quelmid = 0, retourne le début
	// quelmid = 1, retourne la fin
	
	// teste si la chaine est assez longue pour etre coupee
	if (chaine.length > cesurepos){
		bestDistance =cesurepos;
		bestSpace = chaine.length;
		for (iCesSplit=0; iCesSplit<chaine.length-1;iCesSplit++){
			if (chaine.charAt(iCesSplit) == " "){
				distance = Math.abs(iCesSplit - cesurepos);
				if (distance < bestDistance){
					bestDistance = distance;
					bestSpace = iCesSplit;
				}
			}
		}
		if (quelmid == 0){// quelmid = 0, retourne le début
			return chaine.substr(0, bestSpace);
		}
		else{// quelmid = 1, retourne la fin
			 return chaine.substr(bestSpace+1);
		}
	}
	// si la chaine est trop courte
	else{
		if (quelmid == 0){// quelmid = 0, retourne le début
			return chaine;
		}
		else{// quelmid = 1, retourne la fin
			 return "";
		}
	}
}

//////////////////////////////
// retourne une matrice [chaine, nbre de lignes]
function splitChaineLongmax(letheme,longmax){
	tabRes=new Array();
	// calcul du nombre de divisions nécessaires
	nSplits = Math.floor((letheme.length) / longmax) + 1;
	returnSplits = 1;
	returnStr = "";	
	for (iSplit=1; iSplit<=nSplits;iSplit++){
		splitPos = longmax;
		if (splitChaineCesure(letheme, splitPos, 1) != ""){// si la chaine est splitable
			returnStr = returnStr + splitChaineCesure(letheme, splitPos, 0) + "\n";
			returnSplits = returnSplits + 1;
			letheme = splitChaineCesure(letheme, splitPos, 1);
			}
		else{
			returnStr = returnStr + splitChaineCesure(letheme, splitPos, 0) ;
			break;
			}
	}
	tabRes.push(returnStr);
	tabRes.push(returnSplits);
	return tabRes;
}
//////////////////////////////

function mainRoutine(){
	
	buildMenu();
	recap();
	pasderes();
	calendrier();
	resetValues();
	
}

//charge le premier fichier xml
if ((global.XMLloaded == false) or (_global.XMLloaded == undefined)){
	trace("XML non charge");
	rawData.load(getMyName("path")+".xml");
}
else{
	trace("XML déjà chargé");
	
	
	mainRoutine();
}

//apres le chargement du xml
rawData.onLoad = function(flag) {
	
	//si le chargement est OK
	if (flag) {
		
		//parse le fichier pour retrouver le type de lexo
		exploreNode( rawData );

		//reset de nodeCount
		nodeCount=new Object();
		
		mainRoutine();
		
		_global.XMLloaded = true;
		
		
	}
	else{
		_global.XMLloaded = false;
	}
}
