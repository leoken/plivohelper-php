<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();
$r->addPlay($base_http."/duck.mp3", 
            array('loop' => 4)
           ); 
$r->Respond();
?>
