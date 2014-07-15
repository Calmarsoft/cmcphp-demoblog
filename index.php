<?php
namespace cmcblog;      // par convetion, nom du site/projet comme namespace

/**
 * 
 * Application entry file
 * 
 * contants: application class instantiate and run
 * 
 * 
 */
/** check environment  **/
$config='';
$doc = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
$srv = filter_input(INPUT_SERVER, 'HTTP_HOST');


if ($doc) {
    if (preg_match('|^C:/.|', $doc) && preg_match('#^localhost(:[\n]+)|()#' , $srv))
        $config = 'wamp';   // windows local
    else if ($srv=='devtest2.home.lan')
        $config = 'prod';   // serveur local
}

if ($config == 'prod')
    ini_set('include_path', 'program:/usr/lib/pear');
else if ($config == 'wamp')
    ini_set('include_path', 'C:\\Users\\Benoit\\Documents\\boulot\\dev\\_srv\\www\\devtest1\\devpt\\cmc;program');
else
    ini_set('include_path', '/var/www/devpt/cmc:program:/usr/lib/pear');


require_once 'myApp.php';


// application create instance
$myApp = myApp::current();
// run application
$myApp->run();

