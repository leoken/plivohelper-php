<?php
    // VERSION: 0.1

    // Plivo REST Helpers
    // ========================================================================

    // ensure Curl is installed
    if(!extension_loaded("curl"))
        throw(new Exception(
            "Curl extension is required for PlivoRestClient to work"));

    /*
     * PlivoRestResponse holds all the REST response data
     * Before using the reponse, check IsError to see if an exception
     * occurred with the data sent to Plivo
     * ResponseXml will contain a SimpleXml object with the response xml
     * ResponseText contains the raw string response
     * Url and QueryString are from the request
     * HttpStatus is the response code of the request
     */
    class PlivoRestResponse {

        public $ResponseText;
        public $ResponseXml;
        public $HttpStatus;
        public $Url;
        public $QueryString;
        public $IsError;
        public $ErrorMessage;

        public function __construct($url, $text, $status) {
            preg_match('/([^?]+)\??(.*)/', $url, $matches);
            $this->Url = $matches[1];
            $this->QueryString = $matches[2];
            $this->ResponseText = $text;
            echo $text;
            $this->HttpStatus = $status;
            if($this->HttpStatus != 204)
                $this->ResponseXml = simplexml_load_string($text);
            echo $this->ResponseXml;
            if($this->IsError = ($status >= 400)) {
              if($status == 401) {
                $this->ErrorMessage = "Authentication required";
              } else {
                $this->ErrorMessage =
                    (string)$this->ResponseXml->RestException->Message;
              }
            }
        }

    }

    /* PlivoRestClient throws PlivoException on error
     * Useful to catch this exception separately from general PHP
     * exceptions, if you want
     */
    class PlivoException extends Exception {}

    /*
     * PlivoRestBaseClient: the core Rest client, talks to the Plivo REST
     * API. Returns a PlivoRestResponse object for all responses if Plivo's
     * API was reachable Throws a PlivoException if Plivo's REST API was
     * unreachable
     */

    class PlivoRestClient {

        protected $Endpoint;
        protected $AccountSid;
        protected $AuthToken;

        /*
         * __construct
         *   $username : Plivo Sid
         *   $password : Plivo AuthToken
         *   $endpoint : The Plivo REST URL
         */
        public function __construct($endpoint, $accountSid, $authToken) {
            $this->AccountSid = $accountSid;
            $this->AuthToken = $authToken;
            $this->Endpoint = $endpoint;
        }

        /*
         * sendRequst
         *   Sends a REST Request to the Plivo REST API
         *   $path : the URL (relative to the endpoint URL, after the /v1)
         *   $method : the HTTP method to use, defaults to GET
         *   $vars : for POST or PUT, a key/value associative array of data to
         * send, for GET will be appended to the URL as query params
         */
        public function request($path, $method = "GET", $vars = array()) {
            $fp = null;
            $tmpfile = "";
            $encoded = "";
            foreach($vars AS $key=>$value)
                $encoded .= "$key=".urlencode($value)."&";
            $encoded = substr($encoded, 0, -1);

            // construct full url
            $url = "{$this->Endpoint}/$path";

            // if GET and vars, append them
            if($method == "GET")
                $url .= (FALSE === strpos($path, '?')?"?":"&").$encoded;

            // initialize a new curl object
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            switch(strtoupper($method)) {
                case "GET":
                    curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                    break;
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, TRUE);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
                    break;
                case "PUT":
                    // curl_setopt($curl, CURLOPT_PUT, TRUE);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    file_put_contents($tmpfile = tempnam("/tmp", "put_"),
                        $encoded);
                    curl_setopt($curl, CURLOPT_INFILE, $fp = fopen($tmpfile,
                        'r'));
                    curl_setopt($curl, CURLOPT_INFILESIZE,
                        filesize($tmpfile));
                    break;
                case "DELETE":
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                    break;
                default:
                    throw(new PlivoException("Unknown method $method"));
                    break;
            }

            // send credentials
            curl_setopt($curl, CURLOPT_USERPWD,
                $pwd = "{$this->AccountSid}:{$this->AuthToken}");

            // do the request. If FALSE, then an exception occurred
            if(FALSE === ($result = curl_exec($curl)))
                throw(new PlivoException(
                    "Curl failed with error " . curl_error($curl)));

            // get result code
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // unlink tmpfiles
            if($fp)
                fclose($fp);
            if(strlen($tmpfile))
                unlink($tmpfile);
            echo "PlivoRestResponse----------";
            return new PlivoRestResponse($url, $result, $responseCode);
        }

        // REST Call Helper
        public function call($vars = array()) {
            $path = "/v0.1/Call/";
            $method = "POST";
            return $this->request($path, $method, $vars);
        }

        // REST Bulk Call Helper
        public function bulk_call($vars = array()) {
            $path = "/v0.1/BulkCalls/";
            $method = "POST";
            return request($path, $method, $vars);
        }

        // REST Transfer Live Call Helper
        public function transfer_call($vars = array()) {
            $path = "/v0.1/TransferCall/";
            $method = "POST";
            return request($path, $method, $vars);
        }

        // REST Hangup Live Call Helper
        public function hangup_call($vars = array()) {
            $path = "/v0.1/HangupCall/";
            $method = "POST";
            return request($path, $method, $vars);
        }

        // REST Hangup All Live Calls Helper
        public function hangup_all_calls() {
            $path = "/v0.1/HangupAllCalls/";
            $method = "POST";
            return request($path, $method);
        }

        // REST Schedule Hangup Helper
        public function schedule_hangup($vars = array()) {
            $path = "/v0.1/ScheduleHangup/";
            $method = "POST";
            return request($path, $method, $vars);
        }

        // REST Cancel a Scheduled Hangup Helper
        public function cancel_scheduled_hangup($vars = array()) {
            $path = "/v0.1/CancelScheduledHangup/";
            $method = "POST";
            return request($path, $method, $vars);
        }
    }

    // RESTXML Response Helpers
    // ========================================================================

    /*
     * Grammar: Base class for all RESTXML grammar elements used in creating Responses
     * Throws a PlivoException if an non-supported attribute or
     * attribute value is added to the grammar. All methods in Grammar are protected
     * or private
     */

    class Grammar {
        private $tag;
        private $body;
        private $attr;
        private $children;

        /*
         * __construct
         *   $body : Grammar contents
         *   $body : Grammar attributes
         */
        function __construct($body=NULL, $attr = array()) {
            if (is_array($body)) {
                $attr = $body;
                $body = NULL;
            }
            $this->tag = get_class($this);
            $this->body = $body;
            $this->attr = array();
            $this->children = array();
            self::addAttributes($attr);
        }

        /*
         * addAttributes
         *     $attr  : A key/value array of attributes to be added
         *     $valid : A key/value array containging the accepted attributes
         *     for this grammar
         *     Throws an exception if an invlaid attribute is found
         */
        private function addAttributes($attr) {
            foreach ($attr as $key => $value) {
                if(in_array($key, $this->valid))
                    $this->attr[$key] = $value;
                else
                    throw new PlivoException($key . ', ' . $value .
                       " is not a supported attribute pair");
            }
        }

        /*
         * append
         *     Nests other grammar elements inside self.
         */
        function append($grammar) {
            if(is_null($this->nesting))
                throw new PlivoException($this->tag ." doesn't support nesting");
            else if(!is_object($grammar))
                throw new PlivoException($grammar->tag . " is not an object");
            else if(!in_array(get_class($grammar), $this->nesting))
                throw new PlivoException($grammar->tag . " is not an allowed grammar here");
            else {
                $this->children[] = $grammar;
                return $grammar;
            }
        }

        /*
         * set
         *     $attr  : An attribute to be added
         *    $valid : The attrbute value for this grammar
         *     No error checking here
         */
        function set($key, $value){
            $this->attr[$key] = $value;
        }

        /* Convenience Methods */
        function addSpeak($body=NULL, $attr = array()){
            return self::append(new Speak($body, $attr));
        }

        function addPlay($body=NULL, $attr = array()){
            return self::append(new Play($body, $attr));
        }

        function addDial($body=NULL, $attr = array()){
            return self::append(new Dial($body, $attr));
        }

        function addNumber($body=NULL, $attr = array()){
            return self::append(new Number($body, $attr));
        }

        function addGetDigits($attr = array()){
            return self::append(new GetDigits($attr));
        }

        function addRecord($attr = array()){
            return self::append(new Record(NULL, $attr));
        }

        function addHangup(){
            return self::append(new Hangup());
        }

        function addRedirect($body=NULL, $attr = array()){
            return self::append(new Redirect($body, $attr));
        }

        function addWait($attr = array()){
            return self::append(new Wait($attr));
        }

        function addConference($body=NULL, $attr = array()){
            return self::append(new Conference($body, $attr));
        }

        function addSms($body=NULL, $attr = array()){
            return self::append(new Sms($body, $attr));
        }

        function addRecordSession($attr = array()){
            return self::append(new RecordSession(NULL, $attr));
        }

        function addPreAnswer($attr = array()){
            return self::append(new PreAnswer(NULL, $attr));
        }

        function addScheduleHangup($attr = array()){
            return self::append(new ScheduleHangup(NULL, $attr));
        }

        /*
         * write
         * Output the XML for this grammar and all it's children
         *    $parent: This grammar's parent grammar
         *    $writeself : If FALSE, Grammar will not output itself,
         *    only its children
         */
        protected function write($parent, $writeself=TRUE){
            if($writeself) {
                $elem = $parent->addChild($this->tag, htmlspecialchars($this->body));
                foreach($this->attr as $key => $value)
                    $elem->addAttribute($key, $value);
                foreach($this->children as $child)
                    $child->write($elem);
            } else {
                foreach($this->children as $child)
                    $child->write($parent);
            }

        }

    }

    class Response extends Grammar {

        private $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Response></Response>";

        protected $nesting = array('Speak', 'Play', 'GetDigits', 'Record',
            'Dial', 'Redirect', 'Wait', 'Hangup', 'Sms', 'RecordSession',
            'PreAnswer', 'ScheduleHangup', 'Conference');

        function __construct(){
            parent::__construct(NULL);
        }

        function Respond($sendHeader = true) {
            // try to force the xml data type
            // this is generally unneeded by Plivo, but nice to have
            if($sendHeader)
            {
                if(!headers_sent())
                {
                    header("Content-type: text/xml");
                }
            }
            $simplexml = new SimpleXMLElement($this->xml);
            $this->write($simplexml, FALSE);
            print $simplexml->asXML();
        }

        function asURL($encode = TRUE){
            $simplexml = new SimpleXMLElement($this->xml);
            $this->write($simplexml, FALSE);
            if($encode)
                return urlencode($simplexml->asXML());
            else
                return $simplexml->asXML();
        }

    }
    /**
    * The <Speak> grammar converts text to speech that is read back to the caller.
    * <Speak> is useful for development or saying dynamic text that is difficult to pre-record.
    */
    class Speak extends Grammar {

        protected $valid = array('voice','language','loop', 'engine', 'method', 'type');
        /**
        * Speak Constructor
        *
        * Instatiates a new Speak object with text and optional attributes.
        * Possible attributes are:
        *   "voice" => 'man'|'woman',
        *   "language" => 'en'|'es'|'fr'|'de',
        *   "loop"  => integer >= 0
        *
        * @param string $text
        * @param array $attr Optional attributes
        * @return Speak
        */
        function __construct($text='', $attr = array()) {
            parent::__construct($text, $attr);
        }
    }
    /**
    * The <Reject> grammar rejects an incoming call to your Plivo number without
    * billing you. This is very useful for blocking unwanted calls.
    * If the first grammar in a RESTXML document is <Reject>, Plivo will not pick
    * up the call. The call ends with a status of 'busy' or 'no-answer',
    * depending on the grammar's 'reason' attribute. Any grammar elements after
    * <Reject> are unreachable and ignored.
    *
    * Note that using <Reject> as the first grammar in your response is the only
    * way to prevent Plivo from answering a call. Any other response will
    * result in an answered call and your account will be billed.
    */
    class Reject extends Grammar {

        protected $valid = array('reason');

        /**
        * Reject Constructor
        *
        * Instatiates a new Reject object with optional attributes.
        * Possible attributes are:
        *   "reason" => 'rejected'|'busy',
        *
        * @param array $attr Optional attributes, defaults to 'rejected'
        * @return Reject
        */
        function __construct($attr = array()) {
            parent::__construct($attr);
        }
    }
    /**
    * The <Play> grammar plays an audio file back to the caller.
    * Plivo retrieves the file from a URL that you provide.
    */
    class Play extends Grammar {

        protected $valid = array('loop');

        /**
        * Play Constructor
        *
        * Instatiates a new Play object with a URL and optional attributes.
        * Possible attributes are:
        *   "loop" =>  integer >= 0
        *
        * @param string $url The URL of an audio file that Plivo will retrieve and play to the caller.
        * @param array $attr Optional attributes
        * @return Play
        */
        function __construct($url='', $attr = array()) {
            parent::__construct($url, $attr);
        }
    }

    /**
    * The <Record> grammar records the caller's voice and returns to you the URL
    * of a file containing the audio recording. You can optionally generate
    * text transcriptions of recorded calls by setting the 'transcribe'
    * attribute of the <Record> grammar to 'true'.
    */
    class Record extends Grammar {

        protected $valid = array('action','method','timeout','finishOnKey',
                                 'maxLength','transcribe','transcribeCallback',
                                 'playBeep', 'format', 'filePath', 'prefix');

        /**
        * Record Constructor
        *
        * Instatiates a new Record object with optional attributes.
        * Possible attributes are:
        *   "action" =>  relative or absolute url, (default: current url)
        *   "method" => 'GET'|'POST', (default: POST)
        *   "timeout" => positive integer, (default: 5)
        *   "finishOnKey"   => any digit, #, * (default: 1234567890*#)
        *   "maxLength" => integer >= 1, (default: 3600, 1hr)
        *   "transcribe" => true|false, (default: false)
        *   "transcribeCallback" => relative or absolute url
        *   "playBeep" => true|false, (default: true)
        *
        * @param array $attr Optional attributes
        * @return Record
        */
        function __construct($attr = array()) {
            parent::__construct($attr);
        }
    }

    /**
    * The <Dial> grammar connects the current caller to an another phone.
    * If the called party picks up, the two parties are connected and can
    * communicate until one hangs up. If the called party does not pick up,
    *  if a busy signal is received, or if the number doesn't exist,
    * the dial grammar will finish.
    *
    * When the dialed call ends, Plivo makes a GET or POST request to
    * the 'action' URL if provided. Call flow will continue using
    * the RESTXML received in response to that request.
    */
    class Dial extends Grammar {

        protected $valid = array('action','method','timeout','hangupOnStar',
            'timeLimit','callerId', 'confirmSound', 'dialMusic', 'confirmKey');

        protected $nesting = array('Number');

        /**
        * Dial Constructor
        *
        * Instatiates a new Dial object with a number and optional attributes.
        * Possible attributes are:
        *   "action" =>  relative or absolute url
        *   "method" => 'GET'|'POST', (default: POST)
        *   "timeout" => positive integer, (default: 30)
        *   "hangupOnStar"  => true|false, (default: false)
        *   "timeLimit" => integer >= 0, (default: 14400, 4hrs)
        *   "callerId" => valid phone #, (default: Caller's callerid)
        *
        * @param string|Number|Conference $number The number or conference you wish to call
        * @param array $attr Optional attributes
        * @return Dial
        */
        function __construct($number='', $attr = array()) {
            parent::__construct($number, $attr);
        }

    }
    /**
    * The <Redirect> grammar transfers control of a call to the RESTXML at a
    * different URL. All grammar elements after <Redirect> are unreachable and ignored.
    */
    class Redirect extends Grammar {

        protected $valid = array('method');

        /**
        * Redirect Constructor
        *
        * Instatiates a new Redirect object with text and optional attributes.
        * Possible attributes are:
        *   "method" => 'GET'|'POST', (default: POST)
        *
        * @param string $url An absolute or relative URL for a different RESTXML document.
        * @param array $attr Optional attributes
        * @return Redirect
        */
        function __construct($url='', $attr = array()) {
            parent::__construct($url, $attr);
        }

    }
    /**
    * The <Wait> grammar waits silently for a specific number of seconds.
    * If <Wait> is the first grammar in a RESTXML document, Plivo will wait
    * the specified number of seconds before picking up the call.
    */
    class Wait extends Grammar {

        protected $valid = array('length');

        /**
        * Wait Constructor
        *
        * Instatiates a new Wait object with text and optional attributes.
        * Possible attributes are:
        *   "length" => integer > 0, (default: 1)
        *
        * @param array $attr Optional attributes
        * @return Wait
        */
        function __construct($attr = array()) {
            parent::__construct(NULL, $attr);
        }

    }
    /**
    * The <Hangup> grammar ends a call. If used as the first grammar in a RESTXML
    * response it does not prevent Plivo from answering the call and billing
    * your account. The only way to not answer a call and prevent billing
    * is to use the <Reject> grammar.
    */
    class Hangup extends Grammar {

        /**
        * Hangup Constructor
        *
        * Instatiates a new Hangup object.
        *
        * @return Hangup
        */
        function __construct() {
            parent::__construct(NULL, array());
        }


    }

    /**
    * The <GetDigits> grammar collects digits that a caller enters into his or her
    * telephone keypad. When the caller is done entering data, Plivo submits
    * that data to the provided 'action' URL in an HTTP GET or POST request,
    * just like a web browser submits data from an HTML form.
    * If no input is received before timeout, <GetDigits> falls through to the
    * next grammar in the RESTXML document.
    *
    * You may optionally nest <Speak> and <Play> within a <GetDigits> grammar while
    * waiting for input. This allows you to read menu options to the caller
    * while letting her enter a menu selection at any time. After the first
    * digit is received the audio will stop playing.
    */
    class GetDigits extends Grammar {

        protected $valid = array('action','method','timeout','finishOnKey',
            'numDigits', 'tries', 'invalidDigitsSound', 'validDigits', 'playBeep');

        protected $nesting = array('Speak', 'Play', 'Wait');
        /**
        * GetDigits Constructor
        *
        * Instatiates a new GetDigits object with optional attributes.
        * Possible attributes are:
        *   "action" =>  relative or absolute url (default: current url)
        *   "method" => 'GET'|'POST', (default: POST)
        *   "timeout" => positive integer, (default: 5)
        *   "finishOnKey"   => any digit, #, *, (default: #)
        *   "numDigits" => integer >= 1 (default: unlimited)
        *
        * @param array $attr Optional attributes
        * @return GetDigits
        */
        function __construct($attr = array()){
            parent::__construct(NULL, $attr);
        }

    }
    /**
    * The <Dial> grammar's <Number> noun specifies a phone number to dial.
    * Using the noun's attributes you can specify particular behaviors
    * that Plivo should apply when dialing the number.
    *
    * You can use multiple <Number> nouns within a <Dial> grammar to simultaneously
    *  call all of them at once. The first call to pick up is connected
    * to the current call and the rest are hung up.
    */
    class Number extends Grammar {

        protected $valid = array('url','sendDigits', 'gateways', 'gatewayCodecs',
                                'gatewayTimeouts', 'gatewayRetries', 'extraDialString');

         /**
        * Number Constructor
        *
        * Instatiates a new Number object with optional attributes.
        * Possible attributes are:
        *   "sendDigits"    => any digits
        *   "url"   => any url
        *
        * @param string $number Number you wish to dial
        * @param array $attr Optional attributes
        * @return Number
        */
         function __construct($number = '', $attr = array()){
            parent::__construct($number, $attr);
         }

    }
    /**
    * The <Dial> grammar's <Conference> noun allows you to connect to a conference
    * room. Much like how the <Number> noun allows you to connect to another
    * phone number, the <Conference> noun allows you to connect to a named
    * conference room and talk with the other callers who have also connected
    * to that room.
    *
    * The name of the room is up to you and is namespaced to your account.
    * This means that any caller who joins 'room1234' via your account will
    * end up in the same conference room, but callers connecting through
    * different accounts would not. The maximum number of participants in a
    * single Plivo conference room is 40.
    */
    class Conference extends Grammar {

        protected $valid = array('muted','beep','startConferenceOnEnter',
            'endConferenceOnExit','waitUrl','waitMethod');

        /**
        * Conference Constructor
        *
        * Instatiates a new Conference object with room and optional attributes.
        * Possible attributes are:
        *   "muted" => true|false, (default: false)
        *   "beef"  => true|false, (default: true)
        *   "startConferenceOnEnter"    => true|false (default: true)
        *   "endConferenceOnExit"   => true|false (default: false)
        *   "waitUrl"   => RESTXML url, empty string, (default: Plivo hold music)
        *   "waitMethod"    => 'GET'|'POST', (default: POST)
        *   "maxParticipants"   => integer > 0 && <= 40 (default: 40)
        *
        * @param string $room Conference room to join
        * @param array $attr Optional attributes
        * @return Conference
        */
         function __construct($room = '', $attr = array()){
            parent::__construct($room, $attr);
         }

    }
    /**
    * The <Sms> grammar sends an SMS message to a phone number during a phone call.
    */
    class Sms extends Grammar {
        protected $valid = array('to', 'from', 'action', 'method', 'statusCallback');

        /**
        * SMS Constructor
        *
        * Instatiates a new SMS object with room and optional attributes.
        * Possible attributes are:
        *   "to"    => phone #
        *   "from"  => sms capable phone #
        *   "action"    => true|false (default: true)
        *   "method"    => 'GET'|'POST', (default: POST)
        *   "statusCallback"    => relative or absolute URL
        *
        * @param string $message SMS message to send
        * @param array $attr Optional attributes
        * @return SMS
        */
         function __construct($message = '', $attr = array()){
            parent::__construct($message, $attr);
         }
    }
    /**
    * The <RecordSession> grammar records the session during a phone call.
    */
    class RecordSession extends Grammar {
        protected $valid = array('prefix', 'format', 'filePath');

         function __construct($message = '', $attr = array()){
            parent::__construct($message, $attr);
         }
    }
    /**
    * The <ScheduleHangup> grammar sets up the call to be scheduled for hangup after a certain time.
    */
    class ScheduleHangup extends Grammar {
        protected $valid = array('time');

         function __construct($message = '', $attr = array()){
            parent::__construct($message, $attr);
         }
    }
    /**
    * The <PreAnswer> grammar sets up the call to be scheduled for hangup after a certain time.
    */
    class PreAnswer extends Grammar {
        protected $valid = array('time');

        protected $nesting = array('Speak', 'Play', 'Wait', 'GetDigits');

         function __construct($message = '', $attr = array()){
            parent::__construct($message, $attr);
         }
    }


    // Plivo Utility function and Request Validation
    // ========================================================================

    class PlivoUtils {

        protected $AccountSid;
        protected $AuthToken;

        function __construct($id, $token){
            $this->AuthToken = $token;
            $this->AccountSid = $id;
        }

        public function validateRequest($expected_signature, $url, $data = array()) {

           // sort the array by keys
           ksort($data);

           // append them to the data string in order
           // with no delimiters
           foreach($data AS $key=>$value)
                   $url .= "$key$value";

           // This function calculates the HMAC hash of the data with the key
           // passed in
           // Note: hash_hmac requires PHP 5 >= 5.1.2 or PECL hash:1.1-1.5
           // Or http://pear.php.net/package/Crypt_HMAC/
           $calculated_signature = base64_encode(hash_hmac("sha1",$url, $this->AuthToken, true));

           return $calculated_signature == $expected_signature;

        }

    }

?>
