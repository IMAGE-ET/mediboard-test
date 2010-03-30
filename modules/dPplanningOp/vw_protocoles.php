<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $dialog;

if($dialog) {
  $can->needsRead();
} else {
  $can->needsEdit();
}

// L'utilisateur est-il chirurgien?
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$chir_id   = $mediuser->isPraticien() ? $mediuser->user_id : null;
$chir_id   = CValue::getOrSession("chir_id", $chir_id);
$code_ccam = CValue::getOrSession("code_ccam");
$page      = intval(CValue::get('page', 0));
// Praticiens et protocoles disponibles
$listPrat   = new CMediusers();
$listPrat   = $listPrat->loadPraticiens(PERM_READ);

$listCodes  = array();

foreach($listPrat as $keyPrat => $valuePrat) {
  $prat =& $listPrat[$keyPrat];
  $prat->loadProtocoles();

  foreach ($prat->_ref_protocoles as $keyProt => $valueProt) {
    $protocole =& $prat->_ref_protocoles[$keyProt];

    if(!$chir_id || ($prat->user_id == $chir_id))
    foreach ($protocole->_codes_ccam as $code) {
      @$listCodes[$code]++;
    }

  
  }
}

// Protocole selectionné
$protSel = new CProtocole;
if($protocole_id = CValue::getOrSession("protocole_id")) {
  $protSel->load($protocole_id);
  $protSel->loadRefs();
}



// Création du template
$smarty = new CSmartyDP();

$smarty->assign("page" , $page );
$smarty->assign("listCodes" , $listCodes );
$smarty->assign("listPrat"  , $listPrat  );
$smarty->assign("protSel"   , $protSel   );
$smarty->assign("chir_id"   , $chir_id   );
$smarty->assign("code_ccam" , $code_ccam );
$smarty->assign("mediuser"  , $mediuser  );

$smarty->display("vw_protocoles.tpl");

?>