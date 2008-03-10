<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductReference', 'reference_id');
$do->createMsg = 'Référence créée';
$do->modifyMsg = 'Référence modifiée';
$do->deleteMsg = 'Référence supprimée';
$do->doIt();

?>