<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision:$
* @author SARL Openxtrem
*/

$cr_id = CValue::get("compte_rendu_id");

$cr = new CCompteRendu();
$cr->load($cr_id);
$cr->loadContent();

echo $cr->_source;

?>
