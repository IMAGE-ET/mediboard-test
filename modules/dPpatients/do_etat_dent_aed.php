<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Fabien Ménager
*/

$class = 'CEtatDent';

$do = new CDoObjectAddEdit($class, 'etat_dent_id');

$do->createMsg = CAppUI::tr("msg-$class-create");
$do->modifyMsg = CAppUI::tr("msg-$class-modify");
$do->deleteMsg = CAppUI::tr("msg-$class-delete");

$do->doIt();

?>