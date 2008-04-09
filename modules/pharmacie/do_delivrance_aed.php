<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage pharmacie
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CDelivrance', 'delivrance_id');
$do->createMsg = 'Delivrance créée';
$do->modifyMsg = 'Delivrance modifiée';
$do->deleteMsg = 'Delivrance supprimée';
$do->doIt();

?>