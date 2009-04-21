<?php

/**
 *	@package Mediboard
 *	@subpackage dPcabinet
 *	@version $Revision$
 *  @author Alexis Granger
 */

$codeacte = mbGetValueFromGetOrSession("code");
$callback = mbGetValueFromGetOrSession("callback");

// Chargement du code
$code = CCodeCCAM::get($codeacte, CCodeCCAM::FULL);

if(!$code->code){
  $tarif = 0;
  $AppUI->stepAjax("$codeacte: code inconnu", UI_MSG_ERROR);
}

// si le code CCAM est complet (activite + phase), on selectionne le tarif correspondant
if($code->_activite != "" && $code->_phase != ""){
  $tarif = $code->activites[$code->_activite]->phases[$code->_phase]->tarif;
} else {
// sinon, on prend le tarif par default
  $tarif = $code->_default;
}
$AppUI->callbackAjax($callback,$tarif);
$AppUI->stepAjax("$codeacte: $tarif");



?>