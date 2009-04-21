<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can, $m;

$can->needsRead();

// Chargement du sejour sélectionné
$sejour = new CSpSejMed();
$sejour->load(mbGetValueFromGetOrSession("sel_numdos"));

// Récuperation des identifiants pour les filtres
$filter = new CSpSejMed();
$filter->numdos = mbGetValueFromGetOrSession("numdos");
$filter->malnum = mbGetValueFromGetOrSession("malnum");
$entree_day   = mbGetValue(mbGetValueFromGetOrSession("debutDay"  ), "__");
$entree_month = mbGetValue(mbGetValueFromGetOrSession("debutMonth"), "__");
$entree_year  = mbGetValue(mbGetValueFromGetOrSession("debutYear" ), "__");
$filter->datent = "$entree_day/$entree_month/$entree_year";
$filter->litcod = mbGetValueFromGetOrSession("litcod");
$filter->pracod = mbGetValueFromGetOrSession("pracod");
$sortie_day   = mbGetValue(mbGetValueFromGetOrSession("finDay"  ), "__");
$sortie_month = mbGetValue(mbGetValueFromGetOrSession("finMonth"), "__");
$sortie_year  = mbGetValue(mbGetValueFromGetOrSession("finYear" ), "__");
$filter->datsor = "$sortie_day/$sortie_month/$sortie_year";

// Clauses where du filtre
$where = array();

if ($filter->numdos) {
  $where["numdos"] = "LIKE '$filter->numdos%'";
}
if ($filter->malnum) {
  $where["malnum"] = "LIKE '$filter->malnum%'";
}
if ($filter->datent != "__/__/__") {
  $where[] = "ASCII(`datent`) LIKE '$filter->datent%'";
}
if ($filter->litcod) {
  $where["litcod"] = "LIKE '$filter->litcod%'";
}
if ($filter->pracod) {
  $where["pracod"] = "LIKE '$filter->pracod%'";
}
if ($filter->datsor != "__/__/__") {
  $where[] = "ASCII(`datsor`) LIKE '$filter->datsor%'";
}

$order = "malnum";

// Chargement des objets filtrés
$sejours = array();
if (count($where)) {
  $sejours = $sejour->loadList($where, $order, "0, 30");
}

// Désélection si le malade n'est pas dans la recherche
if (count($where) && !array_key_exists($sejour->_id, $sejours)) {
  $sejour= new CSpSejMed();
}

// Sélection du premier de la liste si aucun n'est déjà sélectionné
if (!$sejour->_id && count($sejours)) {
  $sejour = reset($sejours);
}

// Chargement de l'id400 associé
$sejour->loadId400();
if ($sejour->_ref_id400->_id) {
  $sejour->_ref_id400->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("datent"  , str_replace("_", "", "$entree_year-$entree_month-$entree_day"));
$smarty->assign("datsor"  , str_replace("_", "", "$sortie_year-$sortie_month-$sortie_day"));
$smarty->assign("sejours"  , $sejours);
$smarty->assign("sejour"   , $sejour );

$smarty->display("view_sejours.tpl");
?>