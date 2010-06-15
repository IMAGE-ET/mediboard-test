<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echange_hprim_id = CValue::get("echange_hprim_id");

$echange_hprim = new CEchangeHprim();
$echange_hprim->load($echange_hprim_id);

$domGetEvenement = new CHPrimXMLEvenementsPatients();
$domGetEvenement->loadXML(utf8_decode($echange_hprim->message));
$domGetEvenement->formatOutput = true;
$doc_errors_msg = @$domGetEvenement->schemaValidate(null, true, false);

$echange_hprim->message = utf8_encode($domGetEvenement->saveXML());
	
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("echange_hprim", $echange_hprim);
$smarty->display("echangeviewer.tpl");

?>