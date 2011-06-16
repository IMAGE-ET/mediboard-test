<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$codeacte     = CValue::get("codeacte");
$object_class = CValue::get("object_class");

$code = CCodeCCAM::get($codeacte, CCodeCCAM::FULL);
$favoris = new CFavoriCCAM();

// Variable permettant de savoir si l'affichage du code complet est necessaire
$codeComplet = false;
$codeacte = $code->code;

if($code->_activite != ""){
  $codeComplet = true;
  $codeacte .= "-$code->_activite";  
  if($code->_phase != ""){
    $codeacte .= "-$code->_phase";
  }
}

$smarty = new CSmartyDP;

$smarty->assign("code", $code);
$smarty->assign("favoris", $favoris);
$smarty->assign("object_class", $object_class);
$smarty->display("inc_vw_detail_ccam.tpl");

?>