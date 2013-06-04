<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$praticien_id = CValue::getOrSession("praticien_id");
$prat = new CMediusers();
$prat->load($praticien_id);

$categorie = new CConsultationCategorie();
$whereCategorie["function_id"] = " = '$prat->function_id'";
$orderCategorie = "nom_categorie ASC";
/** @var CConsultationCategorie[] $categories */
$categories = $categorie->loadList($whereCategorie, $orderCategorie);

// Creation du tableau de categories simplifié pour le traitement en JSON
$listCat = array();

foreach ($categories as $key => $cat) {
  $listCat[$cat->_id] = array(
    "nom_icone"   => $cat->nom_icone,
    "duree"       => $cat->duree,
    "commentaire" => utf8_encode($cat->commentaire));
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("categories", $categories);
$smarty->assign("listCat", $listCat);
$smarty->assign("categorie_id", "");

$smarty->display("httpreq_view_list_categorie.tpl");
