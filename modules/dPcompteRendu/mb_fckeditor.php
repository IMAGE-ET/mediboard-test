<?php

/**
 * Instanciation de CKEditor
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$templateManager = unserialize(gzuncompress($_SESSION["dPcompteRendu"]["templateManager"]));
header("Content-Type: text/javascript");

$user = CMediusers::get();
$use_apicrypt = false;
if (!$user->isPraticien() && CModule::getActive("apicrypt")) {
  $use_apicrypt = true;
}
elseif ($user->mail_apicrypt && CModule::getActive("apicrypt")) {
  $use_apicrypt = true;
}

$use_mssante = false;
if (!$user->isPraticien() && CModule::getActive('mssante')) {
  $use_mssante = true;
}
elseif (CModule::getActive('mssante') && CMSSanteUserAccount::isUserHasAccount($user)) {
  $use_mssante = true;
}

// Création du template
$smarty = new CSmartyDP("modules/dPcompteRendu");

$smarty->assign("templateManager", $templateManager);
$smarty->assign("nodebug", true);
$smarty->assign("use_apicrypt"  , $use_apicrypt);
$smarty->assign('use_mssante'   , $use_mssante);

$smarty->display("mb_fckeditor.tpl");