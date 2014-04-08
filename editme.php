<?php

session_start();

if($_SESSION['loggedin'] != 'yes'){
	header("Location: /editme");
}

require('editme/simple_html_dom.php');

// Create DOM from URL or file
$html = file_get_html($_GET['page']);

$element = $html->getElementByTagName('body');

$element->innertext = $element->innertext.'<script>var WLEpagename = "'.$_GET['page'].'";</script><script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script><script src="editme/editor.php"></script>';

echo $html;

/* make output more readable
$dom = new DOMDocument();
$dom->preserveWhiteSpace = FALSE;
$dom->loadHTML($html);
$dom->formatOutput = TRUE;

echo $dom->saveHTML();
*/


?>