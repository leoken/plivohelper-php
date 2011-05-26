<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();
$r->addSpeak('Hello world'); 

$r->addSpeak('${strepoch()}', 
             array('loop' => 1, 
                   'type' => "CURRENT_DATE_TIME", 
                   'method' => "PRONOUNCED"
                  )
            );
$r->addSpeak('${strepoch()}', 
             array('loop' => 5, 
                   'type' => "CURRENT_TIME", 
                   'method' => "PRONOUNCED"
                  )
            );
$r->Respond();
?>
