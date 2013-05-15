<?php 

/**
 * Search a template field
 *  
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */
 
 CCanDo::checkRead();

$search = CValue::get("search", "%");
$class  = CValue::get("class");
$result = array();

$object = new $class;

if (!$object instanceof CMbObject) {
  CAppUI::stepAjax("class%s-not-instanceof-CMbObject", UI_MSG_ERROR, $class);
}

$template = new CTemplateManager();
$object->fillTemplate($template);

foreach ($template->sections as $_section) {
  foreach ($_section as $_field) {
    if (array_key_exists("field", $_field)) {
      if (strpos($_field["field"], $search) !== false) {
        $result[$_field["field"]] = $_field;
      }
    }
    else {
      foreach ($_field as $_subfield) {
        if (array_key_exists("field", $_subfield)) {
          if (strpos($_subfield["field"], $search) !== false) {
            $result[$_subfield["field"]] = $_subfield;
          }
        }
      }
    }
  }
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("results", $result);
$smarty->display("inc_vw_answer_field_template.tpl");
