<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$object_id      = CValue::getOrSession("object_id");
$object_class   = CValue::getOrSession("object_class");

/* @var CSejour $sejour*/
$sejour = new $object_class;
$sejour->load($object_id);
$sejour->loadRefPatient();
$sejour->loadRefsFactureEtablissement();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("vw_factures_sejour.tpl");
