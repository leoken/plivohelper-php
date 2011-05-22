<?php
    // Include the PHP Plivo Rest library
    require "../plivohelper.php";

    $REST_API_URL = 'http://127.0.0.1:8088';

    // Plivo REST API version
    $ApiVersion = 'v0.1';

    // Set our AccountSid and AuthToken
    $AccountSid = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $AuthToken = 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY';

    // Instantiate a new plivo Rest Client
    $client = new PlivoRestClient($REST_API_URL, $AccountSid, $AuthToken, $ApiVersion);

    // ========================================================================

    # Hangup a call using a HTTP POST
    $hangup_call_params = array(
        'CallUUID' => 'edaa59e1-79e0-41de-b016-f7a7570f6e9c', # Request UUID to hangup call
    );

    try {
        // Hangup call
        $response = $client->hangup_call($hangup_call_params);
        print_r($response);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        exit(0);
    }
