<?php /* $Id: httpreq_do_empty_templates.php 982 2006-09-30 17:52:38Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 982 $
 * @author Thomas Despoix
 * @license GNU GPL 
 **/

global $AppUI, $can, $dPconfig;

$can->needsAdmin();

// Check params
if (null == $dsn = mbGetValueFromGet("dsn")) {
  $AppUI->stepAjax("Aucun DSN spécifié", UI_MSG_ERROR);
}

if (null == @$dPconfig["db"][$dsn]) {
  $AppUI->stepAjax("Configuration pour le DSN '$dsn' inexistante", UI_MSG_ERROR);
}

$dsConfig =& $dPconfig["db"][$dsn];
if ("mysql" != $dbtype = $dsConfig["dbtype"]) {
  $AppUI->stepAjax("Seules les DSN MySQL peuvent être créées par un accès administrateur", UI_MSG_ERROR);
}

// Substitute admin access
$user = $dsConfig["dbuser"];
$pass = $dsConfig["dbpass"];
$name = $dsConfig["dbname"];

$dsConfig["dbuser"] = mbGetValueFromGet("master_user");
$dsConfig["dbpass"] = mbGetValueFromGet("master_pass");
$dsConfig["dbname"] = "";

if (null == $ds = CSQLDataSource::get($dsn)) {
  $AppUI->stepAjax("Connexion en tant qu'admisnitrateur échouée", UI_MSG_ERROR);
}

$AppUI->stepAjax("Connexion en tant qu'admisnitrateur réussie");

foreach ($ds->queriesForDSN($user, $pass, $name) as $key => $query) {
  if (!$ds->exec($query)) {
    $AppUI->stepAjax("Requête '$key' échouée", UI_MSG_WARNING);
    continue;
  }
  
  $AppUI->stepAjax("Requête '$key' effectuée");
}

