<?php

global $dPconfig, $shm;
$root_dir = $dPconfig["root_dir"];
$user_locale = $AppUI->user_locale;
$shared_name = "locales-$user_locale";

// Load from shared memory if possible
if (null == $locales = $shm->get($shared_name)) {
  foreach (glob("$root_dir/locales/$user_locale/*.php") as $file) {
    require_once($file);
  }

  $shm->put($shared_name, $locales);
}

// Encoding definition
require_once("$root_dir/locales/$user_locale/encoding.php");

$GLOBALS["translate"] =& $locales;

?>