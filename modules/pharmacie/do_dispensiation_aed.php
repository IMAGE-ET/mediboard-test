<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage pharmacie
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CDispensiation', 'dispensiation_id');
$do->createMsg = 'Dispensiation créée';
$do->modifyMsg = 'Dispensiation modifiée';
$do->deleteMsg = 'Dispensiation supprimée';
$do->doIt();

?>