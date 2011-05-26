<?php 

// Include the PHP Plivo Rest library
require "../../plivohelper.php";

// @start snippet
/* Define Menu */
$web = array();
$web['default'] = array('receptionist','hours', 'location', 'duck');
$web['location'] = array('receptionist','east-bay', 'san-jose', 'marin');
// @end snippet

/* Get the menu node, index, and url */
/* Get the menu node, index, and url */
$node = isset($_REQUEST['node']) ? $_REQUEST['node']:'default';
$index = isset($_REQUEST['Digits']) ? (int) $_REQUEST['Digits']:0;

$url = 'http://' . dirname($_SERVER["SERVER_NAME"] .  $_SERVER['PHP_SELF']) . '/phonemenu.php';

/* Check to make sure index is valid */
if (isset($web[$node]) || count($web[$node]) >= $index && !is_null($_REQUEST['Digits']))
	$destination = $web[$node][$index];
else
	$destination = NULL;

// @start snippet
/* Render TwiML */
$r = new Response();
switch($destination) {
	case 'hours':
		$r->append(new Speak("Initech is open Monday through Friday, 9am to 5pm"));
		$r->append(new Speak("Saturday, 10am to 3pm and closed on Sundays"));	
		break;
	case 'location':
		$r->append(new Speak("Initech is located at 101 4th St in San Francisco California"));
		$g = $r->append(new Gather(array('action' => 'http://' . dirname($_SERVER["SERVER_NAME"] .  $_SERVER['PHP_SELF']) . '/phonemenu.php?node=location', 'numDigits' => '1')));
		$g->append(new Speak("For directions from the East Bay, press 1"));
		$g->append(new Speak("For directions from San Jose, press 2"));
		break;
	case 'east-bay':
		$r->append(new Speak("Take BART towards San Francisco / Milbrae. Get off on Powell Street. Walk a block down 4th street"));
		break;
	case 'san-jose':
		$r->append(new Speak("Take Cal Train to the Milbrae BART station. Take any Bart train to Powell Street "));
		break;
	case 'duck';
		$r->append(new Play("duck.mp3"));
		break;
	case 'receptionist';
		$r->append(new Speak("Please wait while we connect you"));
		$r->append(new Dial("NNNNNNNNNN"));
		break;
	default:
		$g = $r->append(new Gather(array('action' => 'http://' . dirname($_SERVER["SERVER_NAME"] .  $_SERVER['PHP_SELF']) . '/phonemenu.php?node=default', 'numDigits' => '1')));
		$g->append(new Speak("Hello and welcome to the Initech Phone Menu"));
		$g->append(new Speak("For business hours, press 1"));
		$g->append(new Speak("For directions, press 2"));
		$g->append(new Speak("To hear a duck quack, press 3"));
		$g->append(new Speak("To speak to a receptionist, press 0"));
		break;	
}
// @end snippet

// @start snippet
if($destination && $destination != 'receptionist') {
	$r->append(new Wait());
	$r->append(new Say("Main Menu"));
	$r->append(new Redirect($url));
}
// @end snippet

$r->Respond();

?>
