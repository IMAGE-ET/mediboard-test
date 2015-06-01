<?php

/**
 * View disciplines
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$page    = intval(CValue::get('page', 0));
$inactif = CValue::get("inactif", array());
$type    = CValue::get("type");

$specialite = new CDiscipline();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("inactif", $inactif);
$smarty->assign("page"   , $page);
$smarty->assign("type"   , $type );
$smarty->assign("specialite", $specialite);
$smarty->display("vw_idx_disciplines.tpl");

