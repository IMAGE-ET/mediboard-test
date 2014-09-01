<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
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
  preg_match_all('/a\.button\.([^\:]+)\:before/', $forms_css, $matches);

  $button_classes = array_merge($button_classes, $matches[1]);
}

CMbArray::removeValue("rtl", $button_classes);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('button_classes', array_values($button_classes));
$smarty->display('css_test.tpl');
