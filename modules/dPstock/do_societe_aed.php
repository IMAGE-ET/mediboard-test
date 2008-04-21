<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$class = 'CSociete';

$do = new CDoObjectAddEdit($class, 'societe_id');
$do->createMsg = CAppUI::tr("msg-$class-create");
$do->modifyMsg = CAppUI::tr("msg-$class-modify");
$do->deleteMsg = CAppUI::tr("msg-$class-delete");

$do->doIt();

?>