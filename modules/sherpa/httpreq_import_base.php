<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Thomas Despoix
 */
 
global $can, $dPconfig, $AppUI, $g;

unset($dPconfig["object_handlers"]["CSpObjectHandler"]);
$AppUI->stepAjax("Déactivation du gestionnaire Sherpa", UI_MSG_OK);

$can->needsAdmin();
$spClass = mbGetValueFromGet("class");

if (!is_subclass_of($spClass, "CSpObject")) {
  $AppUI->stepAjax("la classe  '$spClass' n'est pas une class Sherpa", UI_MSG_ERROR);
}

// Filtre sur les enregistrements
$spObject = new $spClass();
$action = mbGetValueFromGet("action", "start");

// Tous les départspossibles
$idMins = array(
  "start"    => "000000",
  "continue" => mbGetValueFromGetOrSession("idContinue"),
  "retry"    => mbGetValueFromGetOrSession("idRetry"),
);

$idMin = mbGetValue(@$idMins[$action], "000000");
mbSetValueToSession("idRetry", $idMin);

// Requêtes
$where = array();
$where[$spObject->_spec->key] = "> '$idMin'";

// Bornes
if ($import_id_min = $dPconfig["sherpa"]["import_id_min"]) {
  $where[] = $spObject->_spec->key." >= '$import_id_min'";
}
if ($import_id_max = $dPconfig["sherpa"]["import_id_max"]) {
  $where[] = $spObject->_spec->key." <= '$import_id_max'";
}

// Comptage
$count = $spObject->countList($where);
$max = $dPconfig["sherpa"]["import_segment"];
$max = min($max, $count);
$AppUI->stepAjax("Import de $max sur $count objets de type '$spClass' à partir de l'ID '$idMin'", UI_MSG_OK);

// Time limit
$seconds = max($max / 20, 60);
$AppUI->stepAjax("Limite de temps du script positionné à '$seconds' secondes", UI_MSG_OK);
set_time_limit($seconds);

// Import réel
$errors = 0;
$spObjects = $spObject->loadList($where, $spObject->_spec->key, "0, $max");
foreach ($spObjects as $_spObject) {
  $mbObject = $_spObject->mapTo();

  // Mapping via l'id400
  $id400 = new CIdSante400;
	$id400->tag = "sherpa group:$g";
	$id400->id400 = $_spObject->_id;

	try {
    $id400->bindObject($mbObject);
	} catch (Exception $e) {
	  $errors++;
    trigger_error($e->getMessage(), E_USER_WARNING);
    $AppUI->stepAjax("Import de '$_spObject->_view' échoué", UI_MSG_WARNING);
	}
}

// Enregistrement du dernier identifiant dans la session
if (@$_spObject->_id) {
  mbSetValueToSession("idContinue", $_spObject->_id);
  $AppUI->stepAjax("Dernier ID traité : '$_spObject->_id'", UI_MSG_OK);
}

$AppUI->stepAjax("Import terminé avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);

?>