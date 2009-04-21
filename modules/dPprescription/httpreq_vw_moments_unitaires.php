<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$code_moment_id = mbGetValueFromGetOrSession("code_moment_id");

// Chargement du moment selectionné
$moment = new CBcbMoment();
$moment->load($code_moment_id);

$moment_complexe = new CMomentComplexe();
$moment_complexe->code_moment_id = $moment->code_moment_id;
$moment_complexe->loadMatchingObject();

// Chargement des associations
$moment->loadRefsAssociations();

// Chargememt de la liste des moments unitaires
$moment_unitaire = new CMomentUnitaire();
$moments_unitaires = $moment_unitaire->loadAllMoments();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("moment", $moment);
$smarty->assign("moment_complexe", $moment_complexe);
$smarty->assign("moments_unitaires", $moments_unitaires);
$smarty->assign("association", new CAssociationMoment());
$smarty->display("inc_vw_moments_unitaires.tpl");
?>