<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

$do = new CDoObjectAddEdit('CProductStockService', 'stock_id');
$do->doIt();

?>