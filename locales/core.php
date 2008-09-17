<?php

global $shm;
$root_dir = CAppUI::conf("root_dir");
$locale = $AppUI->user_prefs["LOCALE"];
$shared_name = "locales-$locale";

// Load from shared memory if possible
if (null == $locales = $shm->get($shared_name)) {
  foreach (glob("$root_dir/locales/$locale/*.php") as $file) {
    require_once($file);
  }

  $shm->put($shared_name, $locales);
}

// Encoding definition
require_once("$root_dir/locales/$locale/encoding.php");

$GLOBALS["translate"] =& $locales;
?>