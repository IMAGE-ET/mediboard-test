<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
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

// @todo : ne passer que $code. Adapter le template en conséquence
$smarty->assign("code", $code);
$smarty->assign("codeComplet"  , $codeComplet);
$smarty->assign("tarif"        , $code->_default);
$smarty->assign("favoris"      , $favoris);
$smarty->assign("codeacte"     , $codeacte);
$smarty->assign("libelle"      , $code->libelleLong);
$smarty->assign("rq"           , $code->remarques);
$smarty->assign("act"          , $code->activites);
$smarty->assign("codeproc"     , $code->procedure["code"]);
$smarty->assign("textproc"     , $code->procedure["texte"]);
$smarty->assign("remboursement", $code->remboursement);
$smarty->assign("forfait"      , $code->forfait);
$smarty->assign("place"        , $code->place);
$smarty->assign("chap"         , $code->chapitres);
$smarty->assign("asso"         , $code->assos);
$smarty->assign("incomp"       , $code->incomps);
$smarty->assign("object_class" , $object_class);
$smarty->assign("hideSelect"   , $hideSelect);
$smarty->display("vw_full_code.tpl");
