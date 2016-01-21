<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');

if($_SESSION['login'] != 'ccitron')
{
    exit('Not allowed.');
}

pre_dump(iconv_get_encoding("all"));
pre_dump(get_include_path());
phpinfo();
?>