<?php /* $Id: print_rapport.php 2321 2007-07-19 08:14:45Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 2321 $
 * @author Romain Ollivier
 */

global $can;
$can->needsRead();

$id400 = new CIdSante400();
$id400->object_class = "CGroups";
$id400->tag = "eCap";
$idGroups = $id400->loadMatchingList();
foreach ($idGroups as $idGroup) {
  $idGroup->loadTargetObject();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("idGroups", $idGroups);

$smarty->display("vw_identifiants.tpl");
?>