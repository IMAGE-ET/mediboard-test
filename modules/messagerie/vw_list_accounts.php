<?php 

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$source = new CSourcePOP();
/** @var CSourcePOP[] $sources */
$sources = $source->loadList();

foreach ($sources as $_source) {
  $_source->loadRefMetaObject();
  $_source->countRefMails();
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("sources", $sources);
$smarty->display("vw_list_accounts.tpl");