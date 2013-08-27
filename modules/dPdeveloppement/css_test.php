<?php /* $Id: form_tester.php 6402 2009-06-08 07:53:07Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: 6402 $
* @author Fabien Ménager
*/

CCanDo::checkRead();

$files = array(
  "style/mediboard/forms.css",
);

$files = array_merge($files, glob("modules/*/css/main.css"));
$button_classes = array();

foreach ($files as $_file) {
  $forms_css = file_get_contents($_file);

  $matches = array();
  preg_match_all("/a\.button\.([^\:]+)\:before/", $forms_css, $matches);

  $button_classes = array_merge($button_classes, $matches[1]);
}

CMbArray::removeValue("rtl", $button_classes);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('button_classes', array_values($button_classes));
$smarty->display('css_test.tpl');
