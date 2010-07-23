<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$where = array();
$where["code"] = "LIKE 'MICRO AY %'";

$product = new CProduct;
$list_product = $product->loadList($where);

CAppUI::stepAjax(count($list_product) . " produit(s) à remplacer");

$errors = 0;
foreach($list_product as $_product) {
  if (!preg_match('/^MICRO AY ([+-])(\d{2}).([05])$/', $_product->code, $matches)) continue;
  
  $old_code = $_product->code;
  
  $dioptrie_sign = ($matches[1] === "+") ? "1": "2";
  $_product->code = "2808" . $dioptrie_sign . $matches[2] . $matches[3];
  
  CAppUI::stepAjax(" Conversion: \"$old_code\" => \"{$_product->code}\"");
  if ($msg = $_product->store()) {
    CAppUI::stepAjax("Problème dans la conversion :" . $msg, UI_MSG_WARNING);
    $errors++;
  }
}

CAppUI::stepAjax("Fin de conversion avec " . $errors . " erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);
CApp::rip();
