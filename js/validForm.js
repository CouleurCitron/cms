// Controle des champs d'un formulaire.
/*
function submitValidForm(idVal){
function fieldvalid(fieldObj) {
function validate_form() {
function CaracMax(texte, maxCar)
function CaracMax_check(texte, maxCar)
function isDate(d) { 
function isEmail(sEmail)
*/

function submitValidForm(idVal){
	if(validate_form(idVal)){
		document.forms[idVal].submit();
	}	
}

var formvalid;


function fieldvalid(fieldObj) { 
	pattern = new RegExp(fieldObj.getAttribute("pattern"));
	str = fieldObj.value;
	if ((fieldObj.id=='login')||(fieldObj.id=='password')||(fieldObj.id=='email')){
		str = trim(str);
	}
	finalValue = str.match(pattern);
	if(finalValue==null)
		finalValue = fieldObj.getAttribute("errorMsg");
	else
		finalValue = "";
	if((finalValue==null) || (finalValue=='null'))
		finalValue = ""; 
	return finalValue;
}


function validate_form(nameForm) { 
	formvalid = '';
	if (nameForm == undefined) nameForm =""; 
	if (nameForm!="" ) {
		
		 
		for(var i=0;i<document.forms[nameForm].elements.length;i++) {
			//alert(.name);
			var obj;
			if (document.forms[nameForm].elements[i]!=null) {
				obj = document.forms[nameForm].elements[i];
				strR = fieldvalid(obj)	
				if (strR!="") {
					formvalid = formvalid + "\n" + strR;
				}
			}
			
		} 

	}
	else {
		for (elt in document.forms[0]) {
		var obj;
		if (document.getElementById(elt)!=null) {
			obj = document.getElementById(elt);
			strR = fieldvalid(obj)	
			if (strR!="") {
				formvalid = formvalid + "\n" + strR;
			}
		}
	}
	}
	
	
	if (formvalid != '') {
		window.alert(formvalid);
		return false;
	} else {
		//if (fieldchecked()) 
			return true;	
		//else 
			//return false;
			
	}
}


	//*** Paramètres
	//*** texte : objet représentant le textarea
	//*** max : nombre de caractères maximum
	// avec message d'alerte et réduction du texte
	function CaracMax(texte, maxCar)
	{
		result = true;

		sTexte = texte.value;

		if (sTexte.length > maxCar)
		{
			alert('Le texte ne doit pas comporter plus de ' + maxCar + ' caractère(s)') ;
			//texte = texte.substr(0, maxCar - 1) ;
			result = false;
		}

		return result;
	}

	//*** Paramètres
	//*** texte : objet représentant le textarea
	//*** max : nombre de caractères maximum
	// contrôle seul du nombre de caractères
	function CaracMax_check(texte, maxCar)
	{
		result = true;

		sTexte = texte.value;

		if (sTexte.length > maxCar)
		{
			result = false;
		}

		return result;
	}


	

function isDate(d) { 
    // Cette fonction permet de vérifier la validité d'une date au format jj/mm/aa ou jj/mm/aaaa 
    // Par Romuald 

    if (d == "") // si la variable est vide on retourne faux 
        return false; 
     
//  e = new RegExp("^[0-9]{1,2}\/[0-9]{1,2}\/([0-9]{2}|[0-9]{4})$"); 
    e = new RegExp("^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$"); 
     
    if (!e.test(d)) // On teste l'expression régulière pour valider la forme de la date 
        return false; // Si pas bon, retourne faux 
  
    // On sépare la date en 3 variables pour vérification, parseInt() converti du texte en entier 
    j = parseInt(d.split("/")[0], 10); // jour 
    m = parseInt(d.split("/")[1], 10); // mois 
    a = parseInt(d.split("/")[2], 10); // année 
  
    // Si l'année n'est composée que de 2 chiffres on complète automatiquement 
    if (a < 1000) { 
        if (a < 89)    a+=2000; // Si a < 89 alors on ajoute 2000 sinon on ajoute 1900 
        else a+=1900; 
    } 
  
    // Définition du dernier jour de février 
    // Année bissextile si annnée divisible par 4 et que ce n'est pas un siècle, ou bien si divisible par 400 
    if (a%4 == 0 && a%100 !=0 || a%400 == 0) fev = 29; 
    else fev = 28; 
  
    // Nombre de jours pour chaque mois 
    nbJours = new Array(31,fev,31,30,31,30,31,31,30,31,30,31); 
  
    // Enfin, retourne vrai si le jour est bien entre 1 et le bon nombre de jours, idem pour les mois, sinon retourn faux 
    return ( m >= 1 && m <=12 && j >= 1 && j <= nbJours[m-1] ); 
} 

// contrôle la validité d'un mail
function isEmail(sEmail){
	var filter=/^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+\.([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(sEmail)){
		return true;
	}			
	else{
		return false;
	}
}

//cocher toutes les cases a cocher de la page
function toggleAll(name) {
//name est le debut du nom des cases a cocher 
//exp: <input type="checkbox" name="cb_act_<?php echo $oAct->get_id(); ?>" ...>
// name est egal a 'cb_act_'
//'user-all' est le nom de la case qui permet de cocher ttes les autres 

	var inputs	= document.getElementsByTagName('input');
	var count	= inputs.length;
	for (i = 0; i < count; i++) {
	
		_input = inputs.item(i);
		if (_input.type == 'checkbox' && _input.id.indexOf(name) != -1) {
		
			_input.checked = document.getElementById('user-all').checked;

		
		}
		
	}
 
} 

function renverseStrDate(sIn) { //1ere procedure renverse date
	var sOut = "";
	sOut = sIn.charAt(6) + sIn.charAt(7) + sIn.charAt(8)+ sIn.charAt(9) + "/" + sIn.charAt(3)+ sIn.charAt(4) + "/" + sIn.charAt(0)+ sIn.charAt(1)
	return(sOut);
} 

