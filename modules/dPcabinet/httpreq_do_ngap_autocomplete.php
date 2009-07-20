<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author Alexis Granger
 */

$ds = CSQLDataSource::get("ccamV2");

$object_id = mbGetValueFromGet("object_id");
$object_class = mbGetValueFromGet("object_class");
$code = mbGetValueFromPost("code");

// Chargement de l'object
$object = new $object_class;
$object->load($object_id);

// Chargement de ces actes NGAP
$object->countActes();

$sql = null;

// Creation de la requete permettant de retourner tous les codes correspondants
if ($code) {
  $sql = "SELECT * 
    FROM `codes_ngap` 
    WHERE `code` LIKE '".addslashes($code)."%' ";

  if (!$object->_count_actes)
    $sql .= " AND `lettre_cle` = '1' ";
}

$result = $ds->loadList($sql, null);

// Création du template
$smarty = new CSmartyDP();
$smarty->debugging = false;

$smarty->assign("code"      , $code);
$smarty->assign("result"    , $result);
$smarty->assign("nodebug", true);

$smarty->display("httpreq_do_ngap_autocomplete.tpl");

?>