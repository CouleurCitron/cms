
// pour confirmer un changement de page (pour une suppression par exemple)
function redirect(URL, confirmMsg) {
	if (confirm(confirmMsg)) {
		document.location = URL;
	}
}
