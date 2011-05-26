<?php 


// Include the PHP Plivo Rest library
require "./plivohelper.php";

$base_http = "http://".dirname($_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"]);

/* Render RESTXML */
$r = new Response();

if (($_GET['redirect'] == 'true') || ($_POST['redirect'] == 'true')) {
    $r->addSpeak("Redirect done !");
    $r->addHangup();
} else {
    $r->addRedirect($base_http."/answered.php?redirect=true");
}

$r->Respond();
?>
