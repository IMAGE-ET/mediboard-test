<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CStock", "stock_id");
$do->createMsg = "Stock créé";
$do->modifyMsg = "Stock modifié";
$do->deleteMsg = "Stock supprimé";
$do->doIt();

?>