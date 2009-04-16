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
$chir_id   = mbGetValueFromGetOrSession("chir_id", $chir_id);
$code_ccam = mbGetValueFromGetOrSession("code_ccam");

// Praticiens et protocoles disponibles
$listPrat   = new CMediusers();
$listPrat   = $listPrat->loadPraticiens(PERM_READ);
$protocoles = array(
  'sejour' => array(),
  'interv' => array(),
);
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

    if ((!$code_ccam or in_array($code_ccam, $protocole->_codes_ccam)) and (!$chir_id or $prat->user_id == $chir_id)) {
      $protocole->loadRefsFwd();
      $protocoles[$protocole->for_sejour == 1 ? 'sejour' : 'interv'][] =& $protocole;
    	
    }    
  }
}

ksort($listCodes);
array_unique($listCodes);

// Protocole selectionné
$protSel = new CProtocole;
if ($protocole_id = mbGetValueFromGetOrSession("protocole_id")) {
  $protSel->load($protocole_id);
  $protSel->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("protocoles", $protocoles);
$smarty->assign("listPrat"  , $listPrat  );
$smarty->assign("listCodes" , $listCodes );
$smarty->assign("protSel"   , $protSel   );
$smarty->assign("chir_id"   , $chir_id   );
$smarty->assign("code_ccam" , $code_ccam );
$smarty->assign("mediuser"  , $mediuser  );

$smarty->display("vw_protocoles.tpl");

?>