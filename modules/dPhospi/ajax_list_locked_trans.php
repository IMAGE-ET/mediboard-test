<?php 

/**
 * Visualiser les transmissions d'une cible fermée
 *  
 * @category dPhospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$transmission_id = CValue::get("transmission_id");

$transmission = new CTransmissionMedicale();
$transmission->load($transmission_id);

$trans = new CTransmissionMedicale();
$trans->sejour_id = $transmission->sejour_id;

if ($transmission->libelle_ATC) {
  $trans->libelle_ATC = $transmission->libelle_ATC;
}
else if ($transmission->object_id && $transmission->object_class) {
  $trans->object_class = $transmission->object_class;
  $trans->object_id = $transmission->object_id;
}

$trans = $trans->loadMatchingList("date DESC");

CMbObject::massLoadFwdRef($trans, "sejour_id");
CMbObject::massLoadFwdRef($trans, "user_id");

$transmissions = array();

foreach ($trans as $_trans) {
  $_trans->canDo();
  $_trans->loadRefSejour();
  $_trans->loadRefUser()->loadRefFunction();
  $_trans->loadTargetObject();

  $sort_key = "$_trans->date $_trans->_class $_trans->user_id $_trans->object_id $_trans->object_class $_trans->libelle_ATC";

  $date_before = mbDateTime("-1 SECOND", $_trans->date);
  $sort_key_before = "$date_before $_trans->_class $_trans->user_id $_trans->object_id $_trans->object_class $_trans->libelle_ATC";

  $date_after  = mbDateTime("+1 SECOND", $_trans->date);
  $sort_key_after = "$date_after $_trans->_class $_trans->user_id $_trans->object_id $_trans->object_class $_trans->libelle_ATC";

  // Aggrégation à -1 sec
  if (array_key_exists($sort_key_before, $transmissions)) {
    array_unshift($transmissions[$sort_key_before], $_trans_const);
  }
  // à +1 sec
  else if (array_key_exists($sort_key_after, $transmissions)) {
    array_unshift($transmissions[$sort_key_after], $_trans);
  }
  // au temps exact, ou unique
  else {
    if (!array_key_exists($sort_key, $transmissions)) {
      $transmissions[$sort_key] = array();
    }
    array_unshift($transmissions[$sort_key], $_trans);
  }
}

$smarty = new CSmartyDP();

$smarty->assign("transmission", $transmission);
$smarty->assign("transmissions", $transmissions);

$smarty->display("inc_list_locked_trans.tpl");