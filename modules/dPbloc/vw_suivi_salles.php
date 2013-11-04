<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

/** @var CBlocOperatoire[] $listBlocs */
$listBlocs  = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");

$smarty = new CSmartyDP();
$smarty->assign("blocs", $listBlocs);
$smarty->assign("first_bloc", reset($listBlocs));
$smarty->assign("date", CMbDT::date());
$smarty->display("vw_suivi_salles.tpl");