<?php

/**
 * Widget des documents
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("vw_aides_saisie_help.tpl");
