<?php

/**
 * Onglet de configuration
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */


CCanDo::checkAdmin();

$arch = exec("arch");
$can_64bit = $arch == "x86_64";

$modele = new CCompteRendu();
$where = array();
$where["object_id"] = "IS NULL";
$where["type"] = " = 'body'";
$modeles = $modele->loadList($where, "nom");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("dompdf_installed", file_exists("lib/dompdf/include/dompdf.cls.php"));
$smarty->assign(
  "wkhtmltopdf_installed",
  (file_exists("lib/wkhtmltopdf/wkhtmltopdf-i386") || file_exists("lib/wkhtmltopdf/wkhtmltopdf-amd64"))
);
$smarty->assign("can_64bit", $can_64bit);
$smarty->assign("modeles", $modeles);
$smarty->display('configure.tpl');
