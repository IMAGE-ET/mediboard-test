<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $g;
$can->needsRead();

// Chargement su séjour s'il y en a un
$sejour = new CSejour();
$sejour->load(mbGetValueFromGet("sejour_id"));
$etabSelected = $sejour->etablissement_transfert_id;

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("etabSelected", $etabSelected);
$smarty->assign("listEtab", $listEtab);

$smarty->display("httpreq_vw_etab_externes.tpl");

?>