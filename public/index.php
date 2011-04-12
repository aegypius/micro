<?php
/*
   PHP Micro Framework
*/
error_reporting(E_ALL | E_STRICT);

/* Configuration ---------------------------------------------------- */
define('APP_PATH',             realpath('..'));
define('APP_CONTROLLERS_PATH', APP_PATH . '/controllers');
define('APP_MODELS_PATH',      APP_PATH . '/models');
define('APP_VIEWS_PATH',       APP_PATH . '/views');
define('FRAMEWORK_PATH',       realpath('../libraries'));

/* Expanding path --------------------------------------------------- */
set_include_path(implode(PATH_SEPARATOR, array(
   APP_PATH,
   APP_CONTROLLERS_PATH,
   APP_MODELS_PATH,
   APP_VIEWS_PATH,
   FRAMEWORK_PATH,
   get_include_path()
)));

require_once 'Micro.php';

/* Bootstrapping ---------------------------------------------------- */
try {
   Micro::bootstrap(APP_PATH, true);
} catch (Exception $e){
   main::error($e);
}
