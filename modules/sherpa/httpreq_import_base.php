<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Thomas Despoix
 */
 
global $can, $dPconfig, $AppUI, $g;

unset($dPconfig["object_handlers"]["CSpObjectHandler"]);
$AppUI->stepAjax("Déactivation du gestionnaire Sherpa", UI_MSG_OK);

$can->needsAdmin();
$spClass = mbGetValueFromGet("class");

if (!class_inherits_from($spClass, "CSpObject")) {
  $AppUI->stepAjax("la classe  '$spClass' n'est pas une class Sherpa", UI_MSG_ERROR);
}

$max = 1000;
$spObject = new $spClass();
$count = $spObject->countList();

$AppUI->stepAjax("Import de $max sur $count objets de type '$spClass'", UI_MSG_OK);

$errors = 0;
$spObjects = $spObject->loadList(null, null, "0, $max");
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

$AppUI->stepAjax("Import terminé avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);

?>