<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$do = new CDoObjectAddEdit('CProduct');

if (CValue::post("_duplicate")) {
  $do->doBind();
  $product = $do->_old;
  $product->code .= "-copie";
  $product->name .= " (Copie)";
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
