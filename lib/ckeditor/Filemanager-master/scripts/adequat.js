/**
 *	Filemanager JS core
 *
 *	filemanager.js
 *
 *	@license	MIT License
 *	@author		Jason Huck - Core Five Labs
 *	@author		Simon Georget <simon (at) linea21 (dot) com>
 *	@copyright	Authors
 */

(function($) {
	
	$("#typeressource").change(function() {
		 var str = "";
		$("select option:selected").each(function () {
		str  = $(this).val();
		});  
		parent.location.href = "/backoffice/cms/lib/ckeditor/Filemanager-master/index.php?dir="+str+"&CKEditorFuncNum=1";
		//parent.location.reload();   

	});
 
 
})(jQuery);
