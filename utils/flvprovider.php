<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/autoprepend.php');
/*/
security improved by by TRUI
www.trui.net

Originally posted at www.flashcomguru.com

//*/


//full path to dir with video.
$path = $_SERVER['DOCUMENT_ROOT'];

$seekat = $_GET['position'];
$filename = htmlspecialchars($_GET['file']);

if ( $_SERVER['HTTPS'] != "on" ) 
	$fullfilename = $path.ereg_replace('http://[^/]+(.*)', '\\1', $filename);
else 
	$fullfilename = $path.ereg_replace('https://[^/]+(.*)', '\\1', $filename);
$filename = basename($fullfilename);
$path = str_replace($filename, '', $fullfilename);

$ext=strrchr($filename, '.');
$file = $path . $filename;
 
if((file_exists($file)) && ($ext=='.flv') && (strlen($filename)>2) && (!eregi(basename($_SERVER['PHP_SELF']), $filename)) && (ereg('^[^./][^/]*$', $filename)))
{
        header('Content-Type: video/x-flv');
		header('Cache-Control: private, max-age=0, must-revalidate'); // ajout dans le cas SSL
		header('Pragma: public'); // ajout dans le cas SSL
        if($seekat != 0) {
                print('FLV');
                print(pack('C', 1 ));
                print(pack('C', 1 ));
                print(pack('N', 9 ));
                print(pack('N', 9 ));
        }
        $fh = fopen($file, 'rb');
        fseek($fh, $seekat);
        while (!feof($fh)) {
          //print (fread($fh, filesize($file))); 
		  print (fread($fh, 1024));
        }
        fclose($fh);
}
	else
{
        print('ERORR: The file does not exist'); }
?>