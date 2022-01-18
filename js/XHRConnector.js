/**                     Sirocco -- Administation Office
 *                           -- XHRConnector.js --
 * 
 * Threadsafe XmlHttpRequest objects Manager
 * Acts as a Singleton object.
 *
 * Javascript version : 1.5
 *
 * @category	Tool
 * @author	Luc Thibault <luc@suhali.net>
 * @copyright	2001-2009 - Luc Thibault - SUHALI WEB DESIGN
 * @license	GNU General Public License
 * @version	2.1
 *
 * GNU General Public License 
 * 	This program is free software ; you can redistribute it and/or modify it
 *	under the terms of the GNU General Public License as published by the
 *	Free Software Foundation; either version 2 of the License,
 *	or (at your option) any later version.
 *	
 *	This program is distributed in the hope that it will be useful, but
 *	WITHOUT ANY WARRANTY ; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *	See the GNU General Public License for more details.
 *	
 *	You should have received a copy of the GNU General Public License
 *	along with this program ; if not, write to the
 *	Free Software Foundation, Inc.,
 *	51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA ;
 *	or visit http://www.gnu.org/copy_nLeft/gpl.html
 */

/*


//***** Chargement dans un div

function chargeFichier() {
	// Création de l'objet
	var XHR = new XHRConnector();		
	// Zone à remplir
	XHR.setTarget('zoneCible');
	// Chargement de la page
	// Natures des paramètres			
	// 	+ string, fichier à charger
	// 	+ string, GET ou POST
	// 	+ ref, nom de la fonction de callBack
	XHR.sendAndLoad("fichier.txt", "GET");
	return true;
}

//***** Chargemment d'un fichier XML

// Création de l'objet XHRConnector
var XHR = new XHRConnector();
// Création de l'objet DOM
var myXML = XHR.createXMLObject();
// On indique que c'est cet objet DOM qui sera alimenté par le fichier externe
XHR.setXMLObject(myXML);

XHR.loadXML("sample.xml");

*/

var XHRConnector = {

	stack : new Array,
	debug : false,
	datas : new String,
	target : new String,
	// Objet XML
	xmlObj : Object,
	// Type de comportement au chargement du XML
	xmlLoad : Object,


	/**
	 * Create and hold a new instance XmlHttpRequest
	 *
	 * @param	String		tgt		optional target element for the returned data
	 * @return	Int		stack id of the created instance or null
	 */
	get_XHR : function (tgt) {
		var uniqueID = Math.round(Math.random()*100000);
		var xhr_object = null;
		try {
			xhr_object = new XMLHttpRequest();		
		} catch (error) {
			if (XHRConnector.debug)
				alert('Error while trying to create a new XMLHttpRequest()\n\n' + error);
			try {
				xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (error) {
				if (XHRConnector.debug)
					alert('Error while trying to create a new ActiveXObject("Microsoft.XMLHTTP")\n\n' + error);
				try {
					xhr_object = new ActiveXObject("Msxml2.XMLHTTP");
				} catch (error) {
					if (XHRConnector.debug)
						alert('Error while trying to create a new ActiveXObject("Msxml2.XMLHTTP")\n\n' + error);
					return null
				}
			}
		}
		XHRConnector.stack[uniqueID] = {	request : xhr_object,
						target : (tgt ? document.getElementById(tgt) : XHRConnector.target)	};
		return uniqueID;
	},


	/**
	 * Call a page through XmlHttpRequest
	 *
	 * @param	String		url		page URL tot connect to
	 * @param	String		mode		calling method (GET or POST)
	 * @param	String		msg		processing status message
	 * @param	String		tgt		optional target element for the returned data
	 * @param	Function		callback		optional callback function
	 * @return	Boolean		url call has gone through
	 */
	sendAndLoad : function (url, mode, msg, tgt, callBack) {
		mode = mode.toUpperCase();
		var id = XHRConnector.get_XHR(tgt);
		if (id == null) {
			alert("Your browser won't allow XmlHttpRequest objects...");
			return;
		}
		XHR = XHRConnector.stack[id];
		XHR['request'].open(mode, url, true);
		XHR['request'].onreadystatechange = function() {
		        if (XHRConnector.stack[id]['request'].readyState == 4) {
				if (XHRConnector.stack[id]['request'].responseText != 'SESSION_EXPIRED') {
					if (typeof callBack == 'function')
						callBack(XHRConnector.stack[id]);
		  			else	XHRConnector.display(XHRConnector.stack[id]);
					delete(XHRConnector.stack[id]);
				} else	window.location.href = 'login.php?err_code=session_expired';
			} else if (XHRConnector.stack[id]['request'].readyState == 1) {
				XHRConnector.message(XHRConnector.stack[id], msg);
	      		}
	   	}
		switch (mode) {
			case 'GET' :	try {
						url = (XHRConnector.datas.length > 0) ? url + "?" + XHRConnector.datas : url;
						XHR['request'].send(null);
					} catch (error) {
						if (XHRConnector.debug)
							alert("Failure in transaction with '"+url+"' with GET method");
						return false;
					}
					break;
			case 'POST' :	try {
						XHR['request'].setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						XHR['request'].send(XHRConnector.datas);
					} catch (error) {
						if (XHRConnector.debug)
							alert("Failure in transaction with '"+url+"' with POST method");
						return false;
					}
					break;
			default :	return false;
					break;
	  	}
		return true;
	},


	/**
	 * Submit a form through XmlHttpRequest
	 *
	 * @param	String		frm		form name
	 * @param	String		msg		processing status message
	 * @param	String		tgt		optional target element for the returned data
	 * @param	Function		callback		optional callback function
	 * @return	Boolean		submission has gone through
	 */
	submitForm : function (frm, msg, tgt, callBack) {
		var id = XHRConnector.get_XHR(tgt);
		if (id == null) {
			alert("Your browser won't allow XmlHttpRequest objects...");
			return;
		}
		var frm = document.forms[frm];
		var url = frm.action;
		var data = XHRConnector.formValues(frm, null);
		if (frm.method.toUpperCase() == 'POST')
			var meth = 'POST';
		else	var meth = 'GET';
		if (meth == 'GET') {
			if (url.indexOf('?') > 0)
				url += "&"+data;
			else	url += "?"+data;
		}
		XHR = XHRConnector.stack[id];
		XHR['target'].innerHTML = '';
		XHR['request'].open(meth, url, true);
		if (meth == 'POST') {
			XHR['request'].setRequestHeader("Method", "POST "+url+"?"+data+" HTTP/1.1");
			XHR['request'].setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		} else {
			XHR['request'].setRequestHeader('Accept', 'message/x-formresult');
		}
		XHR['request'].onreadystatechange = function() {
			if (XHRConnector.stack[id]['request'].readyState == 4) {
				if (XHRConnector.stack[id]['request'].responseText != 'SESSION_EXPIRED') {
					if (typeof callBack == 'function')
						callBack(XHRConnector.stack[id]);
		  			else	XHRConnector.display(XHRConnector.stack[id]);
					delete(XHRConnector.stack[id]);
				} else	window.location.href = 'login.php?err_code=session_expired';
			} else if (XHRConnector.stack[id].readyState == 1)
				XHRConnector.message(XHRConnector.stack[id], msg);
		}
		try {
			XHRConnector.stack[id]['request'].send(data);
		} catch (error) {
			if (XHRConnector.debug)
				alert("Failure in submission to '"+url+"' with "+meth+" method");
			return false;
		}
		return true;
	},


	// + ----------------------------------------------------------------------------------
	// + loadXML
	// + Charge un fichier XML
	// + Entrées
	// + 	xml			String		Le fichier XML à charger
	// + ----------------------------------------------------------------------------------
	loadXML : function (xml, callBack) {
		if (!conn)
			return false;
		if (xmlObj && xml) {
			if (typeof callBack == 'function') {
				if (xmlLoad == 'onload') {
					xmlObj.onload = function () {
						callBack(xmlObj);
					}
				} else {
					xmlObj.onreadystatechange = function() {
						if (xmlObj.readyState == 4)
							callBack(xmlObj)
					}
				}
			}
			xmlObj.load(xml);
			return;
		}		
	},


	formValues : function (fobj, valFunc) {
		var str = "";
		var valueArr = null;
		var val = "";
		var cmd = "";
		for (var i=0; i<fobj.elements.length; i++) {
			//alert(fobj.elements[i].name+' : '+fobj.elements[i].type+' : '+fobj.elements[i].value);
			switch(fobj.elements[i].type) {
				case 'hidden'		:	str += fobj.elements[i].name + "=" + fobj.elements[i].value + "&";
								break;
				case 'checkbox'		:	if (fobj.elements[i].checked)
									str += fobj.elements[i].name + "=" + fobj.elements[i].value + "&";
								break;
				case 'text'		:	if (valFunc) {
				       					//use single quotes for argument so that the value of
									//fobj.elements[i].value is treated as a string not a literal
									cmd = valFunc + "(" + 'fobj.elements[i].value' + ")";
									val = eval(cmd);
			       					}
								str += fobj.elements[i].name + "=" + escape(fobj.elements[i].value) + "&";
								break;
				case 'textarea'		:	str += fobj.elements[i].name + "=" + fobj.elements[i].value + "&";
								break;
				case 'select-one'	:	str += fobj.elements[i].name + "=" + fobj.elements[i].options[fobj.elements[i].selectedIndex].value + "&";
								break;
				case 'select-multiple'	:	for (var j=0; j<fobj.elements[i].length; j++) 
									if (fobj.elements[i][j].selected == true)
										str += fobj.elements[i].name + "=" + fobj.elements[i][j].value + "&";
								break;
				case 'radio'	:		if (fobj.elements[i].checked == true)
									str += fobj.elements[i].name + "=" + fobj.elements[i].value + "&";
								break;
		       }
	       }
	       str = str.substr(0,(str.length-1));
	       return str;
	},


	message : function (XHR, msg) {
		if (XHR && msg != '') {
			try {
				XHR['target'].innerHTML = '<span class="common">'+msg+'</span>';
				//alert(XHR['target']);
			} catch (error) {
				if (XHR.debug)
					alert("Failure, '"+XHR['target']+"' is not a valid element");
			}
		}
	},


	display : function (XHR) {
		try {
			XHR['target'].innerHTML = XHR['request'].responseText;
		} catch (error) {
			if (XHR.debug)
				alert("Failure, '"+XHR['target']+"' is not a valid element");
			return;
		}
		// Reassign loaded elements ids, names and class
		var All = XHR['target'].getElementsByTagName("*");
		for (var i=0; i<All.length; i++) {
			All[i].id = All[i].getAttribute("id");
			All[i].name = All[i].getAttribute("name");
			//All[i].className=All[i].getAttribute("class");		// Bugs on IE 6 & 7
		}
		// Force execution of loaded javascript (functions should there be declared as vars : var func = function() {})
		var AllScripts = XHR['target'].getElementsByTagName("script");
		for (var i=0; i<AllScripts.length; i++) {
			var s = AllScripts[i];
			if (s.src && s.src!="")
				eval(getFileContent(s.src));
			else	eval(s.innerHTML);
		}
	},


	// + ----------------------------------------------------------------------------------
	// + setDebug
	// + Active/Désactive l'affichage des exceptions
	// + ----------------------------------------------------------------------------------
	setDebug : function (status) {
		XHRConnector.debug = status;
	},

	// + ----------------------------------------------------------------------------------
	// + resetData
	// + Permet de vider la pile des données
	// + ----------------------------------------------------------------------------------
	resetData : function () {
		XHRConnector.datas = new String();
		XHRConnector.datas = '';
	},
	
	// + ----------------------------------------------------------------------------------
	// + appendData
	// + Permet d'empiler des données afin de les envoyer
	// + ----------------------------------------------------------------------------------
	appendData : function (pfield, pvalue) {
		XHRConnector.datas += (XHRConnector.datas.length == 0) ? pfield+ "=" + escape(pvalue) : "&" + pfield + "=" + escape(pvalue);
	},
	
	// + ----------------------------------------------------------------------------------
	// + setRefreshArea
	// + Indique quel elment identifié par id est valoris lorsque l'objet XHR reoit une réponse
	// + ----------------------------------------------------------------------------------
	setTarget : function (id) {
		XHRConnector.target = document.getElementById(id);
	},
	
	// + ----------------------------------------------------------------------------------
	// + createXMLObject
	// + Méthode permettant de créer un objet DOM, retourne la réfrence
	// + Inspiré de: http://www.quirksmode.org/dom/importxml.html
	// + ----------------------------------------------------------------------------------
	createXMLObject : function () {
		try {
			 	xmlDoc = document.implementation.createDocument('', '', null);
				xmlLoad = 'onload';
		} catch (error) {
			try {
				xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
				xmlLoad = 'onreadystatechange ';
			} catch (error) {
				if (debug)
					alert('Erreur lors de la tentative de création de l\'objet XML\n\n');
				return false;
			}
		}
		return xmlDoc;
	},
	
	// + ----------------------------------------------------------------------------------
	// + Permet de définir l'objet XML qui doit être valorisé lorsque l'objet XHR recoit une réponse
	// + ----------------------------------------------------------------------------------
	setXMLObject : function (obj) {
		if (obj == undefined) {
				if (debug)
					alert('Paramètre manquant lors de l\'appel de la méthode setXMLObject');
				return false;
		}
		try {
			//xmlObj = this.createXMLObject();
			xmlObj = obj;
		} catch (error) {
			if (debug)
				alert('Erreur lors de l\'affectation de l\'objet XML dans la méthode setXMLObject');
		}
	}

}
