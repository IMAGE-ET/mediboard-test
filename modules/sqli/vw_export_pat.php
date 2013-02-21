<?php

/**
 * export the patient database to CSV
 *
 * @category Sqli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

//smarty
$smarty = new CSmartyDP();
$smarty->display("vw_export_pat.tpl");