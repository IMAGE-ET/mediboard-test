<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CSociete', 'societe_id');
$do->createMsg = 'Société créée';
$do->modifyMsg = 'Société modifiée';
$do->deleteMsg = 'Société supprimée';
$do->doIt();

?>