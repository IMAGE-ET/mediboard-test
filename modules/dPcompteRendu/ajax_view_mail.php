<?php

/**
 * Modale des destinataires possibles pour un docitem
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$object_guid = CValue::get("object_guid");

$smarty = new CSmartyDP();

$smarty->assign("object_guid", $object_guid);

$smarty->display("inc_view_mail.tpl");
