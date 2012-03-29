<?php

/**
 * dPsalleOp
 *  
 * @category dPsalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

$object = new $object_class;
$object->load($object_id);

$anesth = new CMediusers();

$non_signes_activite_1 = 0;
$non_signes_activite_4 = 0;

if ($object instanceof CSejour) {
  $object->loadRefPraticien()->loadRefFunction();
  $actes_ccam = $object->loadRefsActesCCAM();
  foreach ($actes_ccam as $_acte_ccam) {
    if ($_acte_ccam->code_activite == 4) {
      $anesth = $_acte_ccam->loadRefExecutant();
      break;
    }
  }
}
  
if ($object instanceof COperation) {
  $object->loadRefChir()->loadRefFunction();
  $object->loadRefPlageOp();
  
  if ($object->_ref_anesth) {
    $object->_ref_anesth->loadRefFunction();
  }
  $anesth = $object->_ref_anesth;
  $actes_ccam = $object->loadRefsActesCCAM();
  
  if (!$anesth->_id) {
    foreach ($actes_ccam as $_acte_ccam) {
      if ($_acte_ccam->code_activite == 4) {
        $anesth = $_acte_ccam->loadRefExecutant();
        break;
      }
    }
  }
}


// Clôture possible que si tous les actes sont signés
$actes_ccam = $object->loadRefsActesCCAM();

foreach ($actes_ccam as $_acte_ccam) {
  if ($_acte_ccam->code_activite == 1 && !$_acte_ccam->signe) {
    $non_signes_activite_1 ++;
  }
  
  if ($_acte_ccam->code_activite == 4 && !$_acte_ccam->signe) {
    $non_signes_activite_4 ++;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("object", $object);
$smarty->assign("anesth", $anesth);
$smarty->assign("non_signes_activite_1", $non_signes_activite_1);
$smarty->assign("non_signes_activite_4", $non_signes_activite_4);

$smarty->display("inc_cloture_activite.tpl");

?>