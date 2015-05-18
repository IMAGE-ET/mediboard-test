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

ksort($templateManager->sections);
foreach ($templateManager->sections as $key => $_section) {
  ksort($templateManager->sections[$key]);
  foreach ($templateManager->sections[$key] as $_key => $_sub_section) {
    ksort($templateManager->sections[$key][$_key]);
  }
}

$user = CMediusers::get();

$use_apicrypt = false;
if (CModule::getActive("apicrypt")) {
  $use_apicrypt = !$user->isPraticien() || $user->mail_apicrypt;
}

$use_mssante = false;
if (CModule::getActive('mssante')) {
  $use_mssante = !$user->isPraticien() || CMSSanteUserAccount::isUserHasAccount($user);
}

// Création du template
$smarty = new CSmartyDP("modules/dPcompteRendu");

$smarty->assign("templateManager", $templateManager);
$smarty->assign("nodebug"        , true);
$smarty->assign("use_apicrypt"   , $use_apicrypt);
$smarty->assign('use_mssante'    , $use_mssante);

$smarty->display("mb_fckeditor.tpl");
