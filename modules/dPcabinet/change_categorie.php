<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefPlageConsult()->loadRefsFwd();
$prat = $consult->_ref_plageconsult->_ref_chir;

$categorie = new CConsultationCategorie();
$whereCategorie["function_id"] = " = '$prat->function_id'";
$orderCategorie = "nom_categorie ASC";
$categories = $categorie->loadList($whereCategorie,$orderCategorie);


// Creation du tableau de categories simplifié pour le traitement en JSON
$listCat = array();

foreach ($categories as $key => $cat){
  $listCat[$cat->_id] = array(
    "nom_icone"   => $cat->nom_icone,
    "duree"       => $cat->duree,
    "commentaire" => utf8_encode($cat->commentaire));
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("consult"   , $consult);
$smarty->assign("listCat"   , $listCat);
$smarty->assign("categories", $categories);
$smarty->display("change_categorie.tpl");
