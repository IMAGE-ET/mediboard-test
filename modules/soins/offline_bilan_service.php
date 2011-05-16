<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = CValue::get("service_id");

// Construction des tokens

$token_cat = "med|inj|perf|aerosol";
$categories = CCategoryPrescription::loadCategoriesByChap(null, "current");

foreach ($categories as $categories_by_chap) {
  foreach($categories_by_chap as $category_id => $_categorie) {
    $token_cat .= "|$category_id";
  }
}

$params = array(
  "token_cat" => $token_cat,
  "service_id" => $service_id
);

// Redirection vers le bilan par service
// avec toutes les catégories cochées sauf les transmissions.
// (entête caché)
CAppUI::redirect("m=dPhospi&a=vw_bilan_service&token_cat=$token_cat&service_id=$service_id&do=1&offline=1&dialog=1");

?>