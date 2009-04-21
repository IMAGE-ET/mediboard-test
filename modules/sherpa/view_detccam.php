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

// Récuperation des identifiants pour les filtres
$filter = new CSpDetCCAM();
$filter->idacte = mbGetValueFromGetOrSession("idacte");
$filter->idinterv = mbGetValueFromGetOrSession("idinterv");
$filter->numdos = mbGetValueFromGetOrSession("numdos");
$filter->malnum = mbGetValueFromGetOrSession("malnum");
$day   = mbGetValue(mbGetValueFromGetOrSession("Day"  ), "__");
$month = mbGetValue(mbGetValueFromGetOrSession("Month"), "__");
$year  = mbGetValue(substr(mbGetValueFromGetOrSession("Year"), 2, 2), "__");
$filter->_date = "$day/$month/$year";
$filter->codpra = mbGetValueFromGetOrSession("codpra");

// Clauses du filtre
$where = array();
if ($filter->idacte) {
  $where[] = "ASCII(`idacte`) = '$filter->idacte'";
}

if ($filter->idinterv != '') {
  $where[] = "ASCII(`idinterv`) = '$filter->idinterv'";
}

if ($filter->numdos) {
  $where["numdos"] = "LIKE '$filter->numdos%'";
}

if ($filter->malnum) {
  $where["malnum"] = "LIKE '$filter->malnum%'";
}

//if ($filter->_date != "__/__/__") {
//  $where[] = "ASCII(`date`) LIKE '$filter->_date%'";
//}

if ($filter->codpra) {
  $where["codpra"] = "LIKE '$filter->codpra%'";
}

$order = "idacte";

$detsccam = $filter->loadList($where, $order, "0,30");

// Chargement du sejour sélectionné
$detccam = new CSpDetCCAM();
$detccam->load(mbGetValueFromGetOrSession("sel_idacte"));

// Désélection si l'entête n'est pas dans la recherche
if (count($where) && !array_key_exists($detccam->_id, $detsccam)) {
  $detccam = new CSpDetCCAM();
}

// Sélection du premier de la liste si aucun n'est déjà sélectionné
if (!$detccam->_id && count($detsccam)) {
  $detccam = reset($detsccam);
}

// Chargement de l'id400 associé
$detccam->loadId400();
if ($detccam->_ref_id400->_id) {
  $detccam->_ref_id400->loadRefsFwd();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"  , str_replace("_", "", "$year-$month-$day"));
$smarty->assign("filter"   , $filter);
$smarty->assign("detccam"  , $detccam);
$smarty->assign("detsccam" , $detsccam);

$smarty->display("view_detccam.tpl");
?>