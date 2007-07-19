<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

$do = new CDoObjectAddEdit("CListeChoix", "liste_choix_id");
$do->createMsg = "Liste cr��e";
$do->modifyMsg = "Liste modifi�e";
$do->deleteMsg = "Liste supprim�e";
$do->doBind();
if (intval(dPgetParam($_POST, 'del'))) {
  $do->doDelete();
  $do->redirect = "m=dPcompteRendu&liste_id=0";
} else {
  $do->doStore();
  $do->redirect = "m=dPcompteRendu&liste_id=".$do->_obj->liste_choix_id;
}
$do->doRedirect();

?>