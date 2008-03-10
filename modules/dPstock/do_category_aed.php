<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductCategory', 'category_id');
$do->createMsg = 'Catégorie créée';
$do->modifyMsg = 'Catégorie modifiée';
$do->deleteMsg = 'Catégorie supprimée';
$do->doIt();

?>