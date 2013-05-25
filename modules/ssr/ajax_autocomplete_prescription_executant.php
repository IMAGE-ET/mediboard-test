<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$libelle = CValue::post("libelle");

// Recuperation de la fonction de l'utilisateur courant
$function_id = CMediusers::get()->function_id;

// Recherche des elements que l'utilisateur courant a le droit de prescrire
// (executant de la categorie et categorie prescritible par executant)
$ljoin = array();
$ljoin["category_prescription"] =
  "category_prescription.category_prescription_id = element_prescription.category_prescription_id";
$ljoin["function_category_prescription"] =
  "function_category_prescription.category_prescription_id = category_prescription.category_prescription_id";

$where = array();
$where["element_prescription.libelle"] = " LIKE '%$libelle%'";
$where["category_prescription.prescription_executant"] = " = '1'";
$where["function_category_prescription.function_category_prescription_id"] = " IS NOT NULL";
$where["function_category_prescription.function_id"] = " = '$function_id'";

$element = new CElementPrescription();
/** @var CElementPrescription[] $elements */
$elements = $element->loadList($where, null, null, null, $ljoin);

// Chargement de la categorie des elements
foreach ($elements as $_element) {
  $_element->loadRefCategory();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("elements", $elements);
$smarty->assign("libelle", $libelle);
$smarty->assign("category_id", "");
$smarty->assign("nodebug", true);
$smarty->display("../../dPprescription/templates/httpreq_do_element_autocomplete.tpl");
