<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();
$r->addConference("plivo",
                  array('muted' => 'false', 
                        'enterSound' => "beep:2",
                        'exitSound' => "beep:1",
                        'startConferenceOnEnter' => "true",
                        'endConferenceOnExit' => "true",
                        'waitSound' => $base_http."/duck.mp3",
                        'timeLimit' => 60,
                        'hangupOnStar' => 'true'
                       )
                 ); 
$r->Respond();
?>
