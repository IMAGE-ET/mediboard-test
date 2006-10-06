<?php

if(!get_magic_quotes_gpc()) {
  foreach($_GET as $key => &$get) {
    $get = addslashes($get);
  }
  unset($get);
  foreach($_POST as $key => &$post) {
    $post = addslashes($post);
  }
  unset($post);
  foreach($_COOKIE as $key => &$cookie) {
    $cookie = addslashes($cookie);
  }
  unset($cookie);
}

?>