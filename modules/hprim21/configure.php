<?php

/**
 * Configuration du module Hprim21
 *
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$hprim21_source = CExchangeSource::get("hprim21", "ftp", true);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("hprim21_source" , $hprim21_source);
$smarty->display("configure.tpl");

