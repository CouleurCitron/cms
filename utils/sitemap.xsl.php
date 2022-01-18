<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
header("Content-type: text/xml; charset=iso-8859-1");

include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_cms.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/cms-inc/include_class.php');

echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?><!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
	<!ENTITY reg    "&#174;">
	<!ENTITY trade  "&#8482;">
	<!ENTITY mdash  "&#8212;">
	<!ENTITY ldquo  "&#8220;">
	<!ENTITY rdquo  "&#8221;"> 
	<!ENTITY pound  "&#163;">
	<!ENTITY yen    "&#165;">
	<!ENTITY euro   "&#8364;">

]> 
  <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="iso-8859-1" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>
    <xsl:template match="/">
      <?php
	  $oSiteToMap = hostToSite($_SERVER['HTTP_HOST']);
	  
	  function callBackXSL($str){
	  
	  	$str = XMLconforme($str);
	  
	  	return preg_replace('/^<\!DOCTYPE[^>]+>/msi', '', $str);
	  
	  }
	  
	  ob_start ("callBackXSL")	;
	  include('content/'.strtolower($oSiteToMap->get_rep()).'/index.php');
	  ob_end_flush();	  
	  ?>	  
    </xsl:template>
  </xsl:stylesheet>