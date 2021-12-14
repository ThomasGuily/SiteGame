<?php


require_once 'vendor/autoload.php'; //Input packet for swift_mailer
$transport = (new Swift_SmtpTransport('localhost', 25)) //test 465
->setUsername('magellan') // a modifier manuellement
->setPassword('Magellanthenavigateur')  // a modifier manuellement
;

