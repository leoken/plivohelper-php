<?php

// @start snippet
/* Define Menu */
$web = array();
$web['default'] = array('receptionist','hours', 'location', 'duck');
$web['location'] = array('receptionist','east-bay', 'san-jose', 'marin');

/* Get the menu node, index, and url */
$node = $_REQUEST['node'];
$index = (int) $_REQUEST['Digits'];
$url = 'http://'.dirname($_SERVER["SERVER_NAME"].$_SERVER['PHP_SELF']).'/phonemenu.php';

/* Check to make sure index is valid */
if(isset($web[$node]) || count($web[$node]) >= $index && !is_null($_REQUEST['Digits']))
    $destination = $web[$node][$index];
else
    $destination = NULL;
// @end snippet

// @start snippet
/* Render RESTXML */
header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Response>\n";
switch($destination) {
    case 'hours': ?>
        <Speak>Initech is open Monday through Friday, 9am to 5pm</Speak>
        <Speak>Saturday, 10am to 3pm and closed on Sundays</Speak>
        <?php break;
    case 'location': ?>
        <Speak>Initech is located at 101 4th St in San Francisco California</Speak>
        <GetDigits action="<?php echo 'http://' . dirname($_SERVER["SERVER_NAME"] .  $_SERVER['PHP_SELF']) . '/phonemenu.php?node=location'; ?>" numDigits="1">
            <Speak>For directions from the East Bay, press 1</Speak>
            <Speak>For directions from San Jose, press 2</Speak>
        </GetDigits>
        <?php break;
    case 'east-bay': ?>
        <Speak>Take BART towards San Francisco / Milbrae. Get off on Powell Street. Walk a block down 4th street</Speak>
        <?php break;
    case 'san-jose': ?>
        <Speak>Take Cal Train to the Milbrae BART station. Take any Bart train to Powell Street</Speak>
        <?php break;
    case 'duck'; ?>
        <Play>duck.mp3</Play>
        <?php break;
    case 'receptionist'; ?>
        <Speak>Please wait while we connect you</Speak>
        <Dial>NNNNNNNNNN</Dial>
        <?php break;
    default: ?>
        <GetDigits action="<?php echo 'http://' . dirname($_SERVER["SERVER_NAME"] .  $_SERVER['PHP_SELF']) . '/phonemenu.php?node=default'; ?>" numDigits="1">
            <Speak>Hello and welcome to the Initech Phone Menu</Speak>
            <Speak>For business hours, press 1</Speak>
            <Speak>For directions, press 2</Speak>
            <Speak>To hear a duck quack, press 3</Speak>
            <Speak>To speak to a receptionist, press 0</Speak>
        </GetDigits>
        <?php
        break;
}
// @end snippet

// @start snippet
if($destination && $destination != 'receptionist') { ?>
    <Wait/>
    <Speak>Main Menu</Speak>
    <Redirect><?php echo 'http://' . dirname($_SERVER["SERVER_NAME"] .  $_SERVER['PHP_SELF']) . '/phonemenu.php' ?></Redirect>
<?php }
// @end snippet

?>

</Response>
