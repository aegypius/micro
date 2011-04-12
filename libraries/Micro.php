<?php
/*
   Micro MVC Framework
*/
class Micro {

   protected function __construct() {
      session_start();
      set_error_handler(    array($this, 'error'));
      set_exception_handler(array($this, 'error'));
      spl_autoload_register(array($this, 'loader'));
   }

   protected function dispatch() {
      $params = array();
      $param = null;
      list($base_uri,$uri)                = array_pad(explode(basename($_SERVER['SCRIPT_NAME']), $_SERVER['REQUEST_URI']), 2, null);
      $this->base_uri  = $base_uri . basename($_SERVER['SCRIPT_NAME']);
      $this->base_path = dirname($this->base_uri);
      if (preg_match('/[\?&=]/xms', $uri)) {
         $uri = preg_replace('/[\?&=]/xms', '/', $_SERVER['REQUEST_URI']);
         header('Location: ' . $uri);
         exit;
      }
      $segments                  = array_pad(explode('/', trim($uri, '/')), 2, null);
      list($controller, $action) = $segments;
      $params = $_REQUEST;
      foreach (array_slice($segments, 2) as $key => $segment) {
         ($key % 2 ? $params[$param] = $segment : $param = $segment);
      }
      if (empty($controller)) $controller = 'main';
      if (empty($action))     $action     = 'index';

      // Calling Controller/Action
      $R = new ReflectionClass($controller);
      $controller = $R->newInstance();

      if (!is_callable(array($controller, $action)))
         throw new Exception("Action $action is not defined!", 404);
      foreach ($params as $key => $value)
         $controller->$key = $value;
      $controller->$action();
   }

   static function loader($class) {
      // PEAR Style Loader

      $paths     = explode(PATH_SEPARATOR, self::getInstance()->app . PATH_SEPARATOR . get_include_path());
      $ext       = pathinfo(__FILE__, PATHINFO_EXTENSION);
      $classfile = str_replace('_', DIRECTORY_SEPARATOR, $class);
      foreach ($paths as $path) {
         $filename = realpath($path) . DIRECTORY_SEPARATOR . $classfile . ".$ext";
         if (is_file($filename)) {
            include_once $filename;
            if (class_exists($class) || interface_exists($class))
               return;
         }
      }
   }

   static function error() {
      switch (func_num_args()) {
         case 1 : {
            $e = func_get_arg(0);
            header(get_class($e) == 'ReflectionException' ? 'HTTP/1.1 404 Not Found' : 'HTTP/1.1 500 Application Error');
            if (self::getInstance()->debug) {
               printf(
                  '<!doctype html><title>An Error Occurred</title><body><h1>%s</h1><p>%s</p><pre>%s</pre></body>',
                  _(get_class($e)),
                  _($e->getMessage()),
                  _($e->getTraceAsString())
               );
            } else {
               printf(
                  '<!doctype html><title>An Error Occurred</title><body><h1>%s</h1><p>%s</p></body>',
                  _(get_class($e)),
                  _($e->getMessage())
               );
            }
            exit;
         }
         default: {
            list($code, $str, $file, $line, $context) = func_get_args();
            throw new ErrorException($str, 0, $code, $file, $line);
            exit;
         }
      }
   }

   static function redirect($uri) {
      $i = self::getInstance();
      header('Location: ' . $i->base_uri . '/' . $uri);
      exit;
   }

   static function bootstrap($app_path, $debug = true) {
      $i = self::getInstance();
      $i->debug = (bool) $debug;
      $i->app   = $app_path;
      return $i->dispatch();
   }

   static $instance;
   static function getInstance() {
      if (!isset(self::$instance))
         self::$instance = new self;
      return self::$instance;
   }
}