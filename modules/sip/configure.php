<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$dest_hprim = new CDestinataireHprim();
$dest_hprim->message = "patients";
$destinataires = $dest_hprim->loadMatchingList();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("destinataires", $destinataires);
$smarty->display("configure.tpl");

?>