<?php
    // Include the PHP PlivoRest library
    require "../plivohelper.php";

    // Set our AccountSid and AuthToken
    $AccountSid = "ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $AuthToken = "YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";

    // Create a new PlivoUtils object
    $utils = new PlivoUtils($AccountSid, $AuthToken);

    // Note, that if your URL uses an implied "index" document
    // (index.php), then apache often adds a slash to the SCRIPT_URI
    // while Plivo's original request will not have a slash
    // Example: if Plivo requested http://mycompany.com/answer
    //   and that url is handled by an index.php script
    //   Apache/PHP will report the URI as being:
    //   http://mycompany.com/answer/
    //   But the hash should be calculated without the trailing slash

    // Also note, if you're using URL rewriting, then you should check
    // to see that PHP is reporting your SCRIPT_URI and
    // QUERY_STRING correctly.

    if($_SERVER['HTTPS'])
        $http = "https://";
    else
        $http = "http://";

    $url = $http.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    if(isset($_POST)) {
        // copy the post data
        $data = $_POST;
    }

    $expected_signature = $_SERVER["HTTP_X_TWILIO_SIGNATURE"];

    echo "The request from Plivo";
    if($utils->validateRequest($expected_signature, $url, $data))
        echo "was confirmed to have come from Plivo.";
    else
        echo "was NOT VALID.  It might have been spoofed!";
?>
