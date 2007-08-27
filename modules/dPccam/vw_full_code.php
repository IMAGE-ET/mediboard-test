<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$codeacte     = mbGetValueFromGetOrSession("codeacte");
$object_class = mbGetValueFromGet("object_class");

$code = new CCodeCCAM($codeacte);
$code->Load();

$favoris = new CFavoriCCAM();

// Création du template
$smarty = new CSmartyDP();


// @todo : ne passer que $code. Adapter le template en conséquence
if($code->activites && $code->activites["1"]->phases){
  $smarty->assign("tarif"        , $code->activites["1"]->phases["0"]->tarif);
}
$smarty->assign("favoris"      , $favoris);
$smarty->assign("codeacte"     , strtoupper($code->code));
$smarty->assign("libelle"      , $code->libelleLong);
$smarty->assign("rq"           , $code->remarques);
$smarty->assign("act"          , $code->activites);
$smarty->assign("codeproc"     , $code->procedure["code"]);
$smarty->assign("textproc"     , $code->procedure["texte"]);
$smarty->assign("place"        , $code->place);
$smarty->assign("chap"         , $code->chapitres);
$smarty->assign("asso"         , $code->assos);
$smarty->assign("incomp"       , $code->incomps);
$smarty->assign("object_class" , $object_class);

$smarty->display("vw_full_code.tpl");

?>