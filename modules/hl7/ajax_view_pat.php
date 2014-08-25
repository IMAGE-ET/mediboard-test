<?php

/**
 * $Id$
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$pat_id = CValue::getOrSession("pat_id");

$patient = new CPatient();
$patient->load($pat_id);
$patient->loadRefsSejours();
$patient->loadRefPhotoIdentite();

//services
$service  = new CService();
$services = $service->loadList();

//lits
$lit  = new CLit();
$lits = $lit->loadList();

$smarty = new CSmartyDP();
$smarty->assign("patient" , $patient);
$smarty->assign("services", $services);
$smarty->assign("lits"    , $lits);
$smarty->display("inc_view_pat.tpl");

