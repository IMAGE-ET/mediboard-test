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


$patient = new CPatient();

//template
$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->display("inc_vw_generator.tpl");