<?php

global $locales;

$localesJSON = json_encode($locales);

header('Content-Type: text/javascript');
echo "window.locales = $localesJSON;";

?>