<?php

/**
 * Recursively add slashes to array of strings
 */
function addslashes_deep(&$var) {
  if (is_array($var)) {
    array_map("addslashes_deep", $var);
  }
  
  if (is_string($var)) {
    $var = addslashes($var);
  }
}
 
// Emulates magic quotes when disabled
if (!get_magic_quotes_gpc()) {
  addslashes_deep($_GET);
  addslashes_deep($_POST);
  addslashes_deep($_COOKIE);
}


?>