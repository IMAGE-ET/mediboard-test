<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;
$can->needsRead();

// Chargement de l'établissement courant
$etablissement = new CSpEtablissement;
$etablissement->load(mbGetAbsValueFromGetOrSession("sp_etab_id"));
$etablissement->loadRefs();

// Chargement de tous les établissements
$etablissements = $etablissement->loadList();
foreach ($etablissements as &$_etablissement) {
  $_etablissement->loadRefs();
}

// Récupération des groupes
$listGroups = new CGroups;
$listGroups = $listGroups->loadList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("etablissement"	, $etablissement);
$smarty->assign("etablissements", $etablissements);
$smarty->assign("listGroups"		, $listGroups);

$smarty->display("view_etablissements.tpl");

?>