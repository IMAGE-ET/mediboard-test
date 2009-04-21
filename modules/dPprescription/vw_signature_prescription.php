<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = mbGetValueFromGet("prescription_id");
$annulation = mbGetValueFromGet("annulation", "0");
$praticien_id = mbGetValueFromGet("praticien_id");

// Chargement des praticiens
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("annulation", $annulation);
$smarty->assign("praticien_id", $praticien_id);
$smarty->display("vw_signature_prescription.tpl");

?>