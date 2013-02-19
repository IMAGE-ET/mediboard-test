<?php

/**
 * return the right tpl for assurance type
 *
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 
CCanDo::checkRead();

$type = CValue::get("type", "assurance_classique");
$consult_id = CValue::get("consult_id");

$consult = new CConsultation();
$consult->_id = $consult_id;
$consult->load();
$consult->loadRefPatient();

//smarty
$smarty = new CSmartyDP();
$smarty->assign("consult", $consult);
$smarty->display("inc_type_assurance_reglement/$type.tpl");