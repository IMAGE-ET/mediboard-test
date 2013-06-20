<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage GestionCab
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$fiche_paie_id = CValue::getOrSession("fiche_paie_id", null);

$fichePaie = new CFichePaie();
$fichePaie->load($fiche_paie_id);

if (!$fichePaie->fiche_paie_id) {
  CAppUI::setMsg("Vous n'avez pas choisi de fiche de paie", UI_MSG_ERROR);
  CAppUI::redirect("m=dPgestionCab&tab=edit_paie");
}

if ($fichePaie->final_file) {
    echo $fichePaie->final_file;
}
else {
  $fichePaie->loadRefsFwd();
  $fichePaie->_ref_params_paie->loadRefsFwd();

  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("fichePaie" , $fichePaie);

  $smarty->display("print_fiche.tpl");
}
