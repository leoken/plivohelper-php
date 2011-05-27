<?php
/*
To run this test suit, you will need to install PHPUnit
for futher information : http://pear.phpunit.de/

On Ubuntu 11.04
sudo apt-get remove phpunit
sudo pear channel-discover pear.phpunit.de
sudo pear channel-discover pear.symfony-project.com
sudo pear channel-discover components.ez.no
sudo pear update-channels
sudo pear upgrade-all
sudo pear install --alldeps phpunit/PHPUnit

On Debian
pear uninstall phpunit/PHPUnit
pear channel-discover components.ez.no
pear channel-discover pear.symfony-project.com
pear install phpunit/PHPUnit

Then run the test suit :
phpunit --verbose restxmlTest.php

*/
    // Testing Requires PHPUnit
    #require_once 'PHPUnit/Framework.php';
    require_once 'PHPUnit/Autoload.php';
    require_once '../plivohelper.php';

    class RESTXMLTest extends PHPUnit_Framework_TestCase
    {
        public function addBadAttribute($verb){
            $r = new $verb($attr = array("foo" => "bar"));
        }
        
        // Test Response Element
        public function testResponseEmpty(){
            $r = new Response();
            $expected = '<Response></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False),
                "Should be an empty response");
        }

        // Test Speak Element
        public function testSpeakBasic() {
            $r = new Response();
            $r->append(new Speak("Hello Monkey"));
            $expected = '<Response><Speak>Hello Monkey</Speak></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testSpeakLoopThree() {
            $r = new Response();
            $r->append(new Speak("Hello Monkey", array("loop" => 3)));
            $expected = '<Response><Speak loop="3">Hello Monkey</Speak></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testSpeakLoopThreeWoman() {
            $r = new Response();
            $r->append(new Speak("Hello Monkey", array("loop" => 3, "voice"=>"woman")));
            $expected = '<Response><Speak loop="3" voice="woman">Hello Monkey</Speak></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testSpeakConvienceMethod() {
            $r = new Response();
            $r->addSpeak("Hello Monkey", array("language" => "fr"));
            $expected = '<Response><Speak language="fr">Hello Monkey</Speak></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testSpeakBadAppend() {
            $this->setExpectedException('PlivoException');
            $s = new Speak();
            $s->append(new Dial());
        }

        public function testSpeakAddBadAttribute(){
            $this->setExpectedException('PlivoException');
            $this->addBadAttribute("Speak");
        }

        //Test Play Element
        public function testPlayBasic() {
            $r = new Response();
            $r->append(new Play("hello-monkey.mp3"));
            $expected = '<Response><Play>hello-monkey.mp3</Play></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testPlayLoopThree() {
            $r = new Response();
            $r->append(new Play("hello-monkey.mp3", array("loop" => 3)));
            $expected = '<Response><Play loop="3">hello-monkey.mp3</Play></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testPlayConvienceMethod() {
            $r = new Response();
            $r->addPlay("hello-monkey.mp3", array("loop" => 3));
            $expected = '<Response><Play loop="3">hello-monkey.mp3</Play></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testPlayBadAppend() {
            $this->setExpectedException('PlivoException');
            $p = new Play();
            $p->append(new Dial());
        }

        public function testPlayAddBadAttribute(){
            $this->setExpectedException('PlivoException');
            $this->addBadAttribute("Play");
        }

        //Test Record Element
        public function testRecord() {
            $r = new Response();
            $r->append(new Record());
            $expected = '<Response><Record></Record></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testRecordMaxLengthKeyTimeout(){
            $r = new Response();
            $r->append(new Record(array("timeout" => 4, "finishOnKey" => "#", "maxLength" => 30)));
            $expected = '<Response><Record timeout="4" finishOnKey="#" maxLength="30"></Record></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testRecordAddAttribute(){
            $r = new Response();
            $re = new Record();
            $re->set("foo", "bar");
            $r->append($re);
            $expected = '<Response><Record foo="bar"></Record></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testRecordBadAppend() {
            $this->setExpectedException('PlivoException');
            $r = new Record();
            $r->append(new Dial());
        }

        public function testRecordAddBadAttribute(){
            $this->setExpectedException('PlivoException');
            $this->addBadAttribute("Record");
        }

        //Test Redirect Element
        public function testRedirect() {
            $r = new Response();
            $r->append(new Redirect());
            $expected = '<Response><Redirect></Redirect></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testRedirectConvience() {
            $r = new Response();
            $r->addRedirect();
            $expected = '<Response><Redirect></Redirect></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }
        public function testRedirectAddAttribute(){
            $r = new Response();
            $re = new Redirect();
            $re->set("foo", "bar");
            $r->append($re);
            $expected = '<Response><Redirect foo="bar"></Redirect></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testRedirectBadAppend() {
            $this->setExpectedException('PlivoException');
            $r = new Redirect();
            $r->append(new Dial());
        }

        public function testRedirectAddBadAttribute(){
            $this->setExpectedException('PlivoException');
            $this->addBadAttribute("Redirect");
        }

        //Test Hangup Element
        public function testHangup() {
            $r = new Response();
            $r->append(new Hangup());
            $expected = '<Response><Hangup></Hangup></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testHangupConvience() {
            $r = new Response();
            $r->addHangup();
            $expected = '<Response><Hangup></Hangup></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testHangupAddAttribute(){
            $r = new Response();
            $re = new Hangup();
            $re->set("foo", "bar");
            $r->append($re);
            $expected = '<Response><Hangup foo="bar"></Hangup></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testHangupBadAppend() {
            $this->setExpectedException('PlivoException');
            $r = new Hangup();
            $r->append(new Dial());
        }

        //Test Wait Element
        public function testWait() {
            $r = new Response();
            $r->append(new Wait());
            $expected = '<Response><Wait></Wait></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testWaitConvience() {
            $r = new Response();
            $r->addWait();
            $expected = '<Response><Wait></Wait></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testWaitAddAttribute(){
            $r = new Response();
            $re = new Wait();
            $re->set("foo", "bar");
            $r->append($re);
            $expected = '<Response><Wait foo="bar"></Wait></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testWaitBadAppend() {
            $this->setExpectedException('PlivoException');
            $r = new Wait();
            $r->append(new Dial());
        }

        public function testWaitAddBadAttribute(){
            $this->setExpectedException('PlivoException');
            new Wait(array("foo" => "bar"));
        }

        //Test Dial Element
        public function testDial() {
            $r = new Response();
            $r->append(new Dial("1231231234"));
            $expected = '<Response><Dial>1231231234</Dial></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testDialConvience() {
            $r = new Response();
            $r->addDial();
            $expected = '<Response><Dial></Dial></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testDialAddNumber() {
            $r = new Response();
            $d = $r->append(new Dial());
            $d->append(new Number("1231231234"));
            $expected = '<Response><Dial><Number>1231231234</Number></Dial></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testDialAddConference() {
            $r = new Response();
            $r->append(new Conference("MyRoom"));
            $expected = '<Response><Conference>MyRoom</Conference></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testDialAddConferenceConvience() {
            $r = new Response();
            $r->addConference("MyRoom", array("startConferenceOnEnter" => "false"));
            $expected = '<Response><Conference startConferenceOnEnter="false">MyRoom</Conference></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testDialAddAttribute(){
            $r = new Response();
            $re = new Dial();
            $re->set("foo", "bar");
            $r->append($re);
            $expected = '<Response><Dial foo="bar"></Dial></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testDialBadAppend() {
            $this->setExpectedException('PlivoException');
            $r = new Dial();
            $r->append(new Wait());
        }

        public function testDialAddBadAttribute(){
            $this->setExpectedException('PlivoException');
            new Dial(array("foo" => "bar"));
        }

        //Test GetDigits Element
        public function testGetDigits(){
            $r = new Response();
            $r->append(new GetDigits());
            $expected = '<Response><GetDigits></GetDigits></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testGetDigitsMethodAction(){
            $r = new Response();
            $r->append(new GetDigits(array("action"=>"example.com", "method"=>"GET")));
            $expected = '<Response><GetDigits action="example.com" method="GET"></GetDigits></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testGetDigitsActionWithParams(){
            $r = new Response();
            $r->append(new GetDigits(array("action" => "record.php?action=recordPageNow&id=4&page=3")));
            $expected = '<Response><GetDigits action="record.php?action=recordPageNow&amp;id=4&amp;page=3"></GetDigits></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testGetDigitsNestedElements(){
            $r = new Response();
            $g = $r->append(new GetDigits(array("action"=>"example.com", "method"=>"GET")));
            $g->append(new Speak("Hello World"));
            $g->append(new Play("helloworld.mp3"));
            $g->append(new Wait());
            $expected = '
                <Response>
                    <GetDigits action="example.com" method="GET">
                        <Speak>Hello World</Speak>
                        <Play>helloworld.mp3</Play>
                        <Wait></Wait>
                    </GetDigits>
                </Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testGetDigitsNestedElementsConvienceMethods(){
            $r = new Response();
            $g = $r->addGetDigits(array("action"=>"example.com", "method"=>"GET"));
            $g->addSpeak("Hello World");
            $g->addPlay("helloworld.mp3");
            $g->addWait();
            $expected = '
                <Response>
                    <GetDigits action="example.com" method="GET">
                        <Speak>Hello World</Speak>
                        <Play>helloworld.mp3</Play>
                        <Wait></Wait>
                    </GetDigits>
                </Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testGetDigitsAddAttribute(){
            $r = new Response();
            $re = new GetDigits();
            $re->set("foo", "bar");
            $r->append($re);
            $expected = '<Response><GetDigits foo="bar"></GetDigits></Response>';
            $this->assertXmlStringEqualsXmlString($expected, $r->asUrl(False));
        }

        public function testGetDigitsBadAppend() {
            $this->setExpectedException('PlivoException');
            $r = new GetDigits();
            $r->append(new Conference());
        }

        public function testGetDigitsAddBadAttribute(){
            $this->setExpectedException('PlivoException');
            new GetDigits(array("foo" => "bar"));
        }
    }

?>
