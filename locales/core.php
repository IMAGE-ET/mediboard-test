<?php

global $dPconfig, $shm;
$root_dir = $dPconfig["root_dir"];
$shared_name = "locales-$AppUI->user_locale";

// Load from shared memory if possible
if (null == $locales = $shm->get($shared_name)) {
  foreach (glob("$root_dir/locales/$AppUI->user_locale/*.php") as $file) {
    require_once($file);
  }

  $shm->put($shared_name, $locales);
}

// Encoding definition
require_once("$root_dir/locales/$AppUI->user_locale/encoding.php");

$GLOBALS["translate"] =& $locales;

?>