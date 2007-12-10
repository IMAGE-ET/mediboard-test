<?php /* $Id: httpreq_do_empty_templates.php 982 2006-09-30 17:52:38Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 982 $
 * @author Thomas Despoix
 * @license GNU GPL 
 **/

global $AppUI, $can;

$can->needsAdmin();

// Check params
if (null == $dsn = mbGetValueFromGet("dsn")) {
  $AppUI->stepAjax("Aucun DSN spécifié", UI_MSG_ERROR);
}

if (!CSQLDataSource::get($dsn)) {
  $AppUI->stepAjax("Connexion vers la DSN '$dsn' échouée", UI_MSG_ERROR);
}

$AppUI->stepAjax("Connexion vers la DSN '$dsn' réussie");