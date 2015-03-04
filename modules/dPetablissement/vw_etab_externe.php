<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$start    = intval(CValue::get("start", 0));
$keywords = CValue::get("keywords");

$etab_externe = new CEtabExterne();
$list_tab_externes   = $etab_externe->seek(($keywords ? $keywords : "%"), null, "$start, 40", true, null, "nom, cp, ville");
$total_etab_externes = $etab_externe->_totalSeek;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("total_etab_externes", $total_etab_externes);
$smarty->assign("list_etab_externes",  $list_tab_externes);
$smarty->assign("current_page",        $start);
$smarty->assign("keywords",            $keywords);

$smarty->display("vw_etab_externe.tpl");
