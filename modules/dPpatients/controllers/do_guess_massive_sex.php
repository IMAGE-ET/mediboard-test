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
$limit = 500;

if ($class == "CCorrespondantPatient" || $class == "CMedecin") {
  /** @var CMedecin|CCorrespondantPatient $obj */
  $field = ($class == "CCorrespondantPatient") ? "sex" : "sexe";

  $idex               = new CIdSante400();
  $idex->object_class = $class;
  $idex->tag          = "sex_guess_last_id";
  $idex->loadMatchingObject();
  $idex->id400 = CMbDT::dateTime();
  $idex->object_id = (($idex->_id && $reset) || !$idex->_id) ? 1 : $idex->object_id;
  $start = $idex->object_id;

  $obj = new $class();
  $key = $obj->_spec->key;
  $where = array();
  $where[] = "$field IS NULL OR $field = 'u'";
  $where["prenom"] = "IS NOT NULL";
  $found = $obj->countList($where);
  echo "\n<div class='small-info'>Objets concernés : ".$found."</div>";
  $where[] = "$key > '$start'";
  $objs = $obj->loadList($where, "$key ASC", "0, $limit");

  if (count($objs)) {
    foreach ($objs as $_obj) {
      $idex->object_id = $_obj->_id;
      if ($msg = $_obj->store()) {
        CAppUI::stepAjax($msg, UI_MSG_WARNING);
      }
      else {
        echo "\n<div class='info'>".$_obj->_view." => ". $_obj->$field."</div>";
      }
    }

    if ($msg = $idex->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
  }
}
else {
  CAppUI::stepAjax("%s_not_managed_by_the_system", UI_MSG_ERROR, $class);
}

CApp::rip();