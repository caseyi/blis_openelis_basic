<?php
#
# Toggles between English and French mode
#

if(session_id() == "")
	session_start();

$target_lang = $_REQUEST['to'];
$_SESSION['locale'] = $target_lang;
setcookie("locale", "$target_lang");//TA: set cookie locale to get it in JS
header("Location: ".$_SERVER['HTTP_REFERER']);
?>
	