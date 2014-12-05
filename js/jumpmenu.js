menuDeroulant = document.getElementById("awsmenuderoulant")
optionsList = menuDeroulant.childNodes;
for (var i = 0; i < optionsList.length; i++) { 
	// do something with each kid as optionsList[i]
	if (document.location.href.indexOf("/"+optionsList[i].value) > 0){
		optionsList[i].selected = true;
	}
}
