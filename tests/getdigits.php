<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();

if (isset($_POST['Digits'])) {
    $digits = $_POST['Digits'];
    if ($digits != "") {
        $r->addSpeak("Get Digits. Digits pressed ".$digits);
    } else {
        $r->addSpeak("Get Digits. No digits pressed");
    }
} else {
    $d = $r->addGetDigits(array("action" => $base_http."/answered.php",
                                "timeout" => 10, "retries" => 2, 
                                "finishOnKey" => "#",
                                "numDigits" => 2, "playBeep" => "true",
                                "validDigits" => "01234")
                         );
    $d->addSpeak("Get Digits. Press 0, 1, 2, 3 or 4");
}

$r->Respond();
?>
