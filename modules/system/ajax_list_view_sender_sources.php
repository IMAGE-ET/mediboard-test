<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Chargement des senders sources
$sender_source = new CViewSenderSource();

/** @var CViewSenderSource[] $senders_source */
$senders_source = $sender_source->loadList(null, "name");
foreach ($senders_source as $_source) {
  $_source->loadRefGroup();
  $_source->loadRefSourceFTP();
  $_source->loadRefSenders();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("senders_source", $senders_source);
$smarty->display("inc_list_view_senders_source.tpl");
