<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();
$p = $r->addPreAnswer();
$p->addSpeak("Hello you are in pre answer mode");
$r->Respond();
?>
