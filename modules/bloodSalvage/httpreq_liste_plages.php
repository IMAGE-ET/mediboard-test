<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;

$can->needsRead();

$date  = CValue::getOrSession("date", mbDate());
$operation_id = CValue::getOrSession("operation_id");
$salle_id = CValue::getOrSession("salle");

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);

// Liste des blocs
$listBlocs = new CBlocOperatoire();
$listBlocs = $listBlocs->loadGroupList();

// Selection des plages opératoires de la journée
$salle = new CSalle;
if ($salle->load($salle_id)) {
  $salle->loadRefsForDay($date); 
}

// Création du template
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