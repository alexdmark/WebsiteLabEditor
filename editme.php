<?php

session_start();

if($_SESSION['loggedin'] != 'yes'){
	header("Location: /editme");
}

require('editme/simple_html_dom.php');

// Create DOM from URL or file
$html = file_get_html($_GET['page']);

$element = $html->getElementByTagName('body');

$scripts = '<script>var WLEpagename = "'.$_GET['page'].'";</script><script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script><script src="editme/editor.php"></script>';

$styles = '
<style>
#WLEbuttons {position:fixed;top:50%;margin-top:-72px;left:0;z-index:99999;}
#WLEpagemanager {background:#34a3cf;}
.WLEbutton-image {width:20px;padding-right:8px;}
.WLEbutton {margin-bottom:0.6em;color:white;border:none;background:#67c036;font:16px sans-serif;padding:10px;outline:none;cursor:pointer;}
</style>';

$element->innertext = $element->innertext.$styles.$scripts;

echo $html;

/* make output more readable
$dom = new DOMDocument();
$dom->preserveWhiteSpace = FALSE;
$dom->loadHTML($html);
$dom->formatOutput = TRUE;

echo $dom->saveHTML();
*/


?>