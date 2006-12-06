<?php
//$chrono = new Chronometer;
//$chrono->start();
ob_start();
  foreach(glob($AppUI->cfg["root_dir"]."/locales/$AppUI->user_locale/*.inc") as $file) {
	  readfile($file);
  }
	eval( "\$GLOBALS['translate']=array(".ob_get_contents()."\n'0');" );
ob_end_clean();
//$chrono->stop();
//mbTrace($chrono);
?>