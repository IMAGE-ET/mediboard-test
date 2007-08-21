<?php

/**
 *	@package Mediboard
 *	@subpackage dPcabinet
 *	@version $Revision:  $
 *  @author Alexis Granger
 */

$codeacte = mbGetValueFromGetOrSession("code");
$callback = mbGetValueFromGetOrSession("callback");
$code = new CCodeCCAM($codeacte);

// Chargement du code
$code->LoadMedium();

if(!$code->code){
  $tarif = 0;
  $AppUI->stepAjax("$codeacte: code inconnu", UI_MSG_ERROR);
}

$tarif = $code->activites["1"]->phases["0"]->tarif;
$AppUI->callbackAjax($callback,$tarif);
$AppUI->stepAjax("$codeacte: $tarif");



?>