<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Chargement de l'item choisi
$siblingItems = array();
$prescriptionItem = new CPrescriptionLaboExamen;
$prescriptionItem->load(CValue::getOrSession("prescription_labo_examen_id"));
if ($prescriptionItem->loadRefs()) {
  $prescriptionItem->_ref_prescription_labo->loadRefsFwd();
  $siblingItems = $prescriptionItem->loadSiblings();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescriptionItem", $prescriptionItem);
$smarty->assign("siblingItems", $siblingItems);
$smarty->assign("time", time());

$smarty->display("inc_graph_resultats.tpl");
