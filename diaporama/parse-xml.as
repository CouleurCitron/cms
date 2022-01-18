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

nbreMaxEntryToList = 100;

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


//-=FONCTIONS XML=-

//cette fonction parcourt le noeud spécifié, fait les opérations nécéssaires
//pour construire l'exercice par rapport a ce qu'elle trouve, et explore tout ses gosses par récursivité
//Param( node XML a explorer)
function exploreNode(curNode){
	//selon le noeud qu'on a fait les opération nécéssaires
	//si c'est un noeud d'arbo
	if( curNode.nodeType==1 ){
		//si c'est le noeud item		
		if( curNode.nodeName.toLowerCase() == "item" ){
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


//charge le premier fichier xml
if ((global.XMLloaded == false) or (_global.XMLloaded == undefined)){
	trace("on charge le XML");
	rawData.load(getMyName("path")+".xml");
}
else{
	trace("XML déjà chargé");	
}


//apres le chargement du xml
rawData.onLoad = function(flag) {
	
	//si le chargement est OK
	if (flag) {
		
		//parse le fichier pour retrouver le type de lexo
		exploreNode( rawData );
		_parent.play();

		//reset de nodeCount
		nodeCount=new Object();
		
		_global.XMLloaded = true;
			
	}
	else{
		_global.XMLloaded = false;
	}
}
