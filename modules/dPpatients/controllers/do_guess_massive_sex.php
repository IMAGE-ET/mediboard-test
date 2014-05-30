<?php 

/**
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$class = CValue::post("target_class");
$reset = CValue::post("reset", 0);
$callback = CValue::post("callback", 0);
$limit = 500;
$use_callback = true;

if (is_subclass_of($class, 'CPerson')) {
  /** @var CPerson $_class */
  $_class = new $class();
  $field_sex = $_class->getSexFieldName();
  if (!$field_sex) {
    CAppUI::stepAjax("class %s does not contain sex field", UI_MSG_ERROR, $class);
  }

  $idex               = new CIdSante400();
  $idex->object_class = $class;
  $idex->tag          = "sex_guess_last_id";
  $idex->loadMatchingObject();
  $idex->id400 = CMbDT::dateTime();
  $idex->object_id = (($idex->_id && $reset) || !$idex->_id) ? 1 : $idex->object_id;
  $start = $idex->object_id;

  /** @var CMbObject $obj */
  $obj = new $class();
  $key = $obj->_spec->key;
  $where = array();
  $where[] = "$field_sex IS NULL OR $field_sex = 'u'";
  $where["prenom"] = "IS NOT NULL";
  $found = $obj->countList($where);
  CAppUI::stepAjax("Objets concernés : ".$found);
  $where[$key] = " > '$start' ";
  $objs = $obj->loadList($where, "$key ASC", "0, $limit");

  if (count($objs)) {
    foreach ($objs as $_obj) {
      $idex->object_id = $_obj->_id;
      if ($msg = $_obj->store()) {
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
      }
      else {
        CAppUI::stepAjax($_obj->$field_sex." | ".$_obj->_view, $_obj->$field_sex == "u" ? UI_MSG_WARNING: UI_MSG_OK);
      }
    }
    if ($msg = $idex->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }
  else {
    $use_callback = false;
  }
}
else {
  CAppUI::stepAjax("%s_not_managed_by_the_system", UI_MSG_ERROR, $class);
}

if ($callback && $use_callback) {
  CAppUI::js("getForm('$callback').onsubmit();");
}

CApp::rip();