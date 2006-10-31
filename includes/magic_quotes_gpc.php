<?php

/**
 * Recursively applies a function to values of an array
 */
function array_map_recursive($function, $array) {
  foreach ($array as $key => $value ) {
    $array[$key] = is_array($value) ? 
      array_map_recursive($function, $value) : 
      $function($value);
  }
  
  return $array;
}

// Emulates magic quotes when disabled
if (!get_magic_quotes_gpc()) {
  $_GET     = array_map_recursive("addslashes", $_GET    );
  $_POST    = array_map_recursive("addslashes", $_POST   );
  $_COOKIE  = array_map_recursive("addslashes", $_COOKIE );
  $_REQUEST = array_map_recursive("addslashes", $_REQUEST);
}

?>