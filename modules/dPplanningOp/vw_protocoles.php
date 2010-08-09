<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/

global $dialog;
if ($dialog) {
  CCanDo::checkRead();
} 
else {
  CCanDo::checkEdit();
}

// L'utilisateur est-il chirurgien ?
$mediuser = CAppUI::$instance->_ref_user;
$chir_id      = CValue::getOrSession("chir_id", $mediuser->isPraticien() ? $mediuser->user_id : null);

$protocole_id = CValue::getOrSession("protocole_id");
$code_ccam    = CValue::getOrSession("code_ccam");
$type         = CValue::getOrSession("type", "interv");
$page         = CValue::get("page", array(
  "sejour" => 0,
  "interv" => 0,
));

// Praticiens, protocoles disponibles
$listPrat   = $mediuser->loadPraticiens(PERM_READ);
$listCodes  = array();
foreach($listPrat as $_prat) {
  $_prat->loadProtocoles();

  foreach ($_prat->_ref_protocoles as $_protocole) {
    if (!$chir_id || ($_prat->user_id == $chir_id))
    foreach ($_protocole->_codes_ccam as $code) {
      @$listCodes[$code]++;
    }
  }
}

// Protocole selectionné
$protocole = new CProtocole;
if ($protocole->load($protocole_id)) {
  $protocole->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("page"      , $page);
$smarty->assign("listCodes" , $listCodes );
$smarty->assign("listPrat"  , $listPrat  );
$smarty->assign("protocole" , $protocole );
$smarty->assign("chir_id"   , $chir_id   );
$smarty->assign("code_ccam" , $code_ccam );
$smarty->assign("mediuser"  , $mediuser  );

$smarty->display("vw_protocoles.tpl");

?>