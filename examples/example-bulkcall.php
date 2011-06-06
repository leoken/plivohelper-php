<?php
    // Include the PHP Plivo Rest library
    require "../plivohelper.php";

    $REST_API_URL = 'http://127.0.0.1:8088';

    // Plivo REST API version
    $ApiVersion = "v0.1";

    // Set our AccountSid and AuthToken
    $AccountSid = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    $AuthToken = "YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";

    // Instantiate a new Plivo Rest Client
    $client = new PlivoRestClient($REST_API_URL, $AccountSid, $AuthToken, $ApiVersion);

    // ========================================================================
    # Define Channel Variable - http://wiki.freeswitch.org/wiki/Channel_Variables
    $extra_dial_string = "bridge_early_media=true,hangup_after_bridge=true";

    # Initiate a new outbound call to user/1000 using a HTTP POST
    $call_params = array(
        'Delimiter' => '>', # Delimter for the bulk list
        'From'=> '919191919191', # Caller Id
        'To' => '1000>1000', # User Numbers to Call separated by delimeter
        'Gateways' => "user/>user/", # Gateway string for each number separated by delimeter
        'GatewayCodecs' => "'PCMA,PCMU'>'PCMA,PCMU'", # Codec string as needed by FS for each gateway separated by delimeter
        'GatewayTimeouts' => "60>30", # Seconds to timeout in string for each gateway separated by delimeter
        'GatewayRetries' => "2>1", # Retry String for Gateways separated by delimeter, on how many times each gateway should be retried
        'ExtraDialString' => $extra_dial_string,
        'AnswerUrl' => "http://127.0.0.1:5000/answered/",
        'HangupUrl' => "http://127.0.0.1:5000/hangup/",
        'RingUrl' => "http://127.0.0.1:5000/ringing/",
    #    'TimeLimit' => '10>30',
    #    'HangupOnRing'=> "0>0",
    );

    try {
        // Initiate various calls
        $response = $client->bulk_call($call_params);
        print_r($response);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        exit(0);
    }

    // check response for success or error
    if($response->IsError)
        echo "Error starting phone calls: {$response->ErrorMessage}\n";
    else
        echo "Started calls: {$response->Response->RequestUUID}\n";
