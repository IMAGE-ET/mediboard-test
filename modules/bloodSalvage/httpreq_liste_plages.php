<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage BloodSalvage
 * @version $Revision: $
 * @author Alexandre Germonneau
 */

global $can, $g;

$can->needsRead();

$date  = mbGetValueFromGetOrSession("date", mbDate());
$operation_id = mbGetValueFromGetOrSession("operation_id");
$salle_id = mbGetValueFromGetOrSession("salle");

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Liste des blocs
$listBlocs = new CBlocOperatoire();
$listBlocs = $listBlocs->loadGroupList();

// Selection des plages op�ratoires de la journ�e
$salle = new CSalle;
if ($salle->load($salle_id)) {
  $salle->loadRefsForDay($date); 
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("vueReduite"    , false        );
$smarty->assign("salle"         , $salle       );
$smarty->assign("praticien_id"  , null         );
$smarty->assign("listBlocs"     , $listBlocs   );
$smarty->assign("listAnesths"   , $listAnesths );
$smarty->assign("date"          , $date        );
$smarty->assign("operation_id"  , $operation_id);

$smarty->display("inc_liste_plages.tpl");
?>