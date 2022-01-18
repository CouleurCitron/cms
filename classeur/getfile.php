
<?php
// Get URL and div
if (!isset($_GET['url'])) { die(); } else { $url = $_GET['url']; }
if (!isset($_GET['el'])) { die(); } else { $el = $_GET['el']; }
// Make sure url starts with http
if (substr($url, 0, 4) != 'http') {
        // Set error
        echo 'alert(\'Security error; incorrect URL!\');';
        die();
}
// Try and get contents
$data = @file_get_contents($url);
if ($data === false) {
        // Set error
        echo 'alert(\'Unable to retrieve "' . $url . '"\');';
        die();
}
// Escape data
$data = str_replace("'", "\'", $data);
$data = str_replace('"', "'+String.fromCharCode(34)+'", $data);
$data = str_replace ("\r\n", '\n', $data);
$data = str_replace ("\r", '\n', $data);
$data = str_replace ("\n", '\n', $data);
?>
el = document.getElementById('<?php echo $el; ?>');
el.innerHTML = '<?php echo $data; ?>'; 