<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();
$r->addRecord(array('timeout' => 5, 'finishOnKey' => "#",
                    'maxLength' => 30, 'playBeep' => 'true',
                    'filePath' => '/tmp')
             ); 
$r->Respond();
?>
