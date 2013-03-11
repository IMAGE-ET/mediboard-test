<?php

/**
 * Add, edit, remove sectorisations rules
 *
 * @category DPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */
 
 CCanDo::checkAdmin();

global $g;
$regleSector = new CRegleSectorisation();
$regleSector->group_id = $g;

$regles = $regleSector->loadMatchingList();

/**
 * @var CRegleSectorisation $_regle
 */
CStoredObject::massLoadFwdRef($regles, "praticien_id");
CStoredObject::massLoadFwdRef($regles, "service_id");
CStoredObject::massLoadFwdRef($regles, "service_id");

foreach ($regles as $_regle) {
  $_regle->loadRefGroup();
  $_regle->loadRefPraticien();
  $_regle->loadRefService();
  $_regle->_ref_praticien->loadRefFunction();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("regles", $regles);
$smarty->assign("active", CAppUI::conf("dPplanningOp CRegleSectorisation use_sectorisation"));
$smarty->display("vw_sectorisations.tpl");