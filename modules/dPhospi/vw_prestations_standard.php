<?php 

/**
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */


$group = CGroups::loadCurrent();

// Récupération des prestations
$presta = new CPrestation;
$presta->group_id = $group->_id;
$prestations = $presta->loadMatchingList("nom");
foreach ($prestations as $_prestation) {
  $_prestation->loadRefGroup();
  $_prestation->loadRefsNotes();
}

// Liste des Etablissements
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prestations"   , $prestations);
$smarty->assign("prestation"    , $presta);
$smarty->assign("etablissements", $etablissements);

$smarty->display("vw_prestation_standard.tpl");