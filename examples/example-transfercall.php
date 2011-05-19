<?php
    // Include the PHP Plivo Rest library
    require "../plivohelper.php";
    
    $REST_API_URL = 'http://127.0.0.1:8088';
    
    // Plivo REST API version 
    $ApiVersion = 'v0.1';
    
    // Set our AccountSid and AuthToken 
    $AccountSid = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $AuthToken = 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY';
    
    // Instantiate a new Twilio Rest Client 
    $client = new PlivoRestClient($REST_API_URL, $AccountSid, $AuthToken, $ApiVersion);
    
    // ========================================================================
    
    # Hangup a call using a HTTP POST
    $transfer_call_params = array(
        'URL' => "http://127.0.0.1:5000/transfered/",
        'CallUUID' => 'edaa59e1-79e0-41de-b016-f7a7570f6e9c', # Request UUID to hangup call
    );
    
    try {
        // Transfer call
        $response = $client->transfer_call($transfer_call_params);
        print_r($response);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        exit(0);
    }
