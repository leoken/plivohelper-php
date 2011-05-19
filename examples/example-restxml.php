<?php

    /*
    The RESTXML PHP Response Library makes it easy to write RESTXML without having
    to touch XML. Error checking is built in to help preventing invalid markup.

    USAGE:
    To create RESTXML, you will make new RESTXML verbs and nest them inside another
    RESTXML verb. Convenience methods are provided to simplify RESTXML creation.
    */

    include ('../plivohelper.php');

    // ========================================================================
    // Using Speak, Dial, and Play
    $r = new Response();
    $r->append(new Speak("Hello World", array("voice" => "man",
        "language" => "fr", "loop" => "10")));
    $r->append(new Dial("4155551212", array("timeLimit" => "45")));
    $r->append(new Play("http://www.mp3.com"));
    $r->Respond();

    /* outputs:
    <Response>
        <Speak voice="man" language="fr" loop="10">Hello World</Speak>
        <Play>http://www.mp3.com</Play>
        <Dial timeLimit="45">4155551212</Dial>
    </Response>
    */

    // The same XML can be created above using the convencience methods
    $r = new Response();
    $r->addSpeak("Hello World", array("voice" => "man", "language" => "fr",
        "loop" => "10"));
    $r->addDial("4155551212", array("timeLimit" => "45"));
    $r->addPlay("http://www.mp3.com");
    //$r->Respond();

    // ========================================================================
    // GetDigits, Redirect
    $r = new Response();
    $g = $r->append(new GetDigits(array("numDigits" => "1")));
    $g->append(new Speak("Press 1"));
    $r->append(new Redirect());
    //$r->Respond();


    /* outputs:
    <Response>
        <GetDigits numDigits="1">
            <Speak>Press 1</Speak>
        </GetDigits>
        <Redirect/>
    </Response>
    */

    // ========================================================================
    // Add a Speak verb multiple times
    $r = new Response();
    $say = new Speak("Press 1");
    $r->append($say);
    $r->append($say);
    //$r->Respond();


    /*
    <Response>
        <Speak>Press 1</Speak>
        <Speak>Press 1</Speak>
    </Response>
    */

    // ========================================================================
    // Creating a Conference Call
    // See the conferencing docs for more information
    // http://www.twilio.com/docs/api/twiml/conference
    $r = new Response();
    $conf = new Conference('MyRoom',array('startConferenceOnEnter'=>"true"));
    $r->append($conf);
    $r->Respond();

    /*
    <Response>
        <Dial>
            <Conference startConferenceOnEnter="True">
                MyRoom
            </Conference>
        </Dial>
    </Response>
    */

    // ========================================================================
    // Set any attribute / value pair
    // You may want to add an attribute to a verb that the library does not
    // support. This can be accomplished by putting __ in front o the
    // attribute name
    $r = new Response();
    $redirect = new Redirect();
    $redirect->set("crazy","delicious");
    $r->append($redirect);
    $r-> Respond();

    /*
    <Response>
        <Redirect crazy="delicious"/>
    </Response>
    */
