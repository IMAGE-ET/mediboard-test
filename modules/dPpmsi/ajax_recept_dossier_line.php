<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 */

CCanDo::checkRead();
$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadRefPatient();
$sejour->loadRefPraticien();
$sejour->loadNDA();

$smarty = new CSmartyDP();

$smarty->assign("_sejour" , $sejour);

$smarty->display("reception_dossiers/inc_recept_dossier_line.tpl");