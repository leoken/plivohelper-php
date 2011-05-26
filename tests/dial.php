<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();

if (isset($_POST['HangupCause']) && isset($_POST['RingStatus'])) {
    $hangup_cause = $_POST['HangupCause'];
    $ring_status = $_POST['RingStatus'];
    $r->addSpeak("Dial Hangup Cause is ".$hangup_cause);
    $r->addSpeak("Dial Ring Status is ".$ring_status);
    $r->addSpeak("Dial Ended");
} else {
    $r->addSpeak("Dial Test");
    $d = $r->addDial(array('action' => $base_http."/answered.php", 
                           'timeLimit' => 60,
                           'hangupOnStar' => 'true'
                          )
                     ); 

    $d->addNumber("4871", 
                 array('gateways' => "sofia/gateway/pstn", 
                       'gatewayTimeouts' => 30
                      )
                );

    $d->addNumber("1749", 
                 array('gateways' => "sofia/gateway/pstn", 
                       'gatewayTimeouts' => 30
                      )
                );
}

$r->Respond();
?>
