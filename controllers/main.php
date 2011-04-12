<?php

class main {
   function index() {
      echo 'Hello, World !';
	 }
	 
	 static function error($error = false) {
      $args = array();
      if ($error) {
         header('HTTP/1.1 '. ($error->getCode() == '404' ? '404 Not Found' : '500 Application Error'));
         $args = array(
            'error' => array(
               'code'    => 'File Not Found',
               'message' => $error->getMessage()
            )
         );
      }
   }
}
