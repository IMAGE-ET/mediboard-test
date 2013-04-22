<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
CCanDo::checkEdit();
$_date_min      = CValue::post("_date_min");
$_date_max      = CValue::post("_date_max");
$type_relance   = CValue::post("type_relance", 0);
$facture_class  = CValue::post("facture_class");
$chirSel        = CValue::post("chir");

if ($_date_min) {
  $where = array();
  if ($facture_class == "CFactureEtablissement") {
    $where["temporaire"] = " = '0'";
  }
  $where["praticien_id"] =" = '$chirSel' ";
  $where["cloture"] = "<= '$_date_max'";
  
  $facture  = new $facture_class;
  $factures = $facture->loadList($where);
  
  foreach ($factures as $key => $_facture) {
  $_facture->loadRefsObjects();
  $_facture->loadRefsReglements();
  $_facture->loadRefsRelances();
    if (!$_facture->_is_relancable || count($_facture->_ref_relances)+1 < $type_relance) {
      unset($factures[$key]);
    }
  }
  
  if (count($factures)) {
    foreach ($factures as $_facture) {
      $relance = new CRelance();
      $relance->object_id    = $_facture->_id;
      $relance->object_class = $_facture->_class;
      if ($msg = $relance->store()) {
        return $msg;
      }
    }
  }
  CAppUI::setMsg(count($factures)." relance(s) crée(s)", UI_MSG_OK);
  echo CAppUI::getMsg();
  CApp::rip();
}
else {
  $do = new CDoObjectAddEdit("CRelance");
  $do->doIt();
}
