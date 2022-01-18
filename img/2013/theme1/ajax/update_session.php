<?php
session_start();

$state = $_POST['state'];

if($state == "close_menu"){
    $_SESSION['BO']['menu'] = 'close';
} else {
    $_SESSION['BO']['menu'] = 'open';
}