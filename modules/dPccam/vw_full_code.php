<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$codeacte     = CValue::getOrSession("_codes_ccam");
$object_class = CValue::get("object_class");
$hideSelect   = CValue::get("hideSelect", 0);

$code = CCodeCCAM::get($codeacte, CCodeCCAM::FULL);

// Variable permettant de savoir si l'affichage du code complet est necessaire
$codeComplet = false;
$codeacte = $code->code;

if ($code->_activite != "") {
  $codeComplet = true;
  $codeacte .= "-$code->_activite";  
  if ($code->_phase != "") {
    $codeacte .= "-$code->_phase";
  }
}

$codeacte = strtoupper($codeacte);

$favoris = new CFavoriCCAM();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("code"         , $code);
$smarty->assign("codeComplet"  , $codeComplet);
$smarty->assign("favoris"      , $favoris);
$smarty->assign("codeacte"     , $codeacte);
$smarty->assign("object_class" , $object_class);
$smarty->assign("hideSelect"   , $hideSelect);

$smarty->display("vw_full_code.tpl");
