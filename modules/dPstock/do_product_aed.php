<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CProduct');

if (CValue::post("_duplicate")) {
  $do->doBind();
  $product = $do->_objBefore;
  $product->code = "";
  $product->_id = null;
  if ($msg = $product->store()) {
    CAppUI::setMsg($msg);
  }
  else {
    // Redirection vers le nouveau 
    $_GET["product_id"] = $product->_id;
  }
}
else {
  $do->doIt();
}
